<?php

namespace App\Filament\Resources\RentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Equipment;

class RentItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'rentItems';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('equipment_id')
                    ->relationship(
                        name: 'equipment',
                        titleAttribute: 'model_name',
                        modifyQueryUsing: function (\Illuminate\Database\Eloquent\Builder $query, RelationManager $livewire) {
                            // Get the current rent record
                            $rent = $livewire->getOwnerRecord();

                            // Get equipment IDs that are already rented during this period
                            $unavailableEquipmentIds = \App\Models\RentItem::query()
                                ->join('rents', 'rent_items.rent_id', '=', 'rents.id')
                                ->where('rents.id', '!=', $rent->id) // Exclude current rent
                                ->where(function ($q) use ($rent) {
                                    // Equipment is rented during the desired period if:
                                    // 1. Rental starts during our period
                                    // 2. Rental ends during our period
                                    // 3. Rental spans our entire period
                                    $q->where(function ($query) use ($rent) {
                                        $query->where('rents.departure_date', '>=', $rent->departure_date)
                                            ->where('rents.departure_date', '<=', $rent->arrival_date);
                                    })->orWhere(function ($query) use ($rent) {
                                        $query->where('rents.arrival_date', '>=', $rent->departure_date)
                                            ->where('rents.arrival_date', '<=', $rent->arrival_date);
                                    })->orWhere(function ($query) use ($rent) {
                                        $query->where('rents.departure_date', '<=', $rent->departure_date)
                                            ->where('rents.arrival_date', '>=', $rent->arrival_date);
                                    });
                                })
                                ->pluck('rent_items.equipment_id')
                                ->toArray();

                            // Get equipment IDs that are already added to this rent
                            $alreadyAddedEquipmentIds = $rent->rentItems->pluck('equipment_id')->toArray();

                            // Exclude unavailable equipment and already added equipment
                            return $query->whereNotIn('id', array_merge($unavailableEquipmentIds, $alreadyAddedEquipmentIds));
                        }
                    )
                    ->preload()
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn($state) => null),
                Forms\Components\Select::make('rent_type')
                    ->required()
                    ->live()
                    ->options(function (Forms\Get $get) {
                        $equipmentId = $get('equipment_id');
                        if (!$equipmentId) {
                            return [];
                        }

                        $equipment = Equipment::find($equipmentId);
                        if (!$equipment) {
                            return [];
                        }

                        $options = [];

                        if ($equipment->acel_rate_dry) {
                            $options['acel_rate_dry'] = 'ACEL Rate (Dry)';
                        }

                        if ($equipment->acel_rate_hour) {
                            $options['acel_rate_hour'] = 'ACEL Rate (Hour)';
                        }

                        if ($equipment->nsjbi_rate_dry) {
                            $options['nsjbi_rate_dry'] = 'NSJBI Rate (Dry)';
                        }

                        if ($equipment->nsjbi_rate_hour) {
                            $options['nsjbi_rate_hour'] = 'NSJBI Rate (Hour)';
                        }

                        if ($equipment->bare_month) {
                            $options['bare_month'] = 'Bare Month';
                        }

                        if ($equipment->per_trip) {
                            $options['per_trip'] = 'Per Trip';
                        }

                        return $options;
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $equipmentId = $get('equipment_id');
                        if (!$equipmentId || !$state) {
                            return;
                        }

                        $equipment = Equipment::find($equipmentId);
                        if (!$equipment) {
                            return;
                        }

                        $rate = $equipment->{$state} ?? 0;
                        $set('rate', $rate);
                    }),
                Forms\Components\TextInput::make('rate')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->placeholder('Rate will be auto-filled based on equipment and rent type')
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $quantity = floatval($get('quantity') ?: 1);
                        $rate = floatval($state ?: 0);
                        $discount = floatval($get('estimated_discount') ?: 0);

                        $totalAmount = ($rate * $quantity) - $discount;
                        $set('total_amount', number_format($totalAmount, 2, '.', ''));
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $quantity = floatval($state ?: 1);
                        $rate = floatval($get('rate') ?: 0);
                        $discount = floatval($get('estimated_discount') ?: 0);

                        $totalAmount = ($rate * $quantity) - $discount;
                        $set('total_amount', number_format($totalAmount, 2, '.', ''));
                    }),
                Forms\Components\TextInput::make('estimated_discount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $quantity = floatval($get('quantity') ?: 1);
                        $rate = floatval($get('rate') ?: 0);
                        $discount = floatval($state ?: 0);

                        $totalAmount = ($rate * $quantity) - $discount;
                        $set('total_amount', number_format($totalAmount, 2, '.', ''));
                    }),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('equipment.model_name')
                    ->label('Equipment'),
                Tables\Columns\TextColumn::make('rent_type')
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'acel_rate_dry' => 'ACEL Rate (Dry)',
                            'acel_rate_hour' => 'ACEL Rate (Hour)',
                            'nsjbi_rate_dry' => 'NSJBI Rate (Dry)',
                            'nsjbi_rate_hour' => 'NSJBI Rate (Hour)',
                            'bare_month' => 'Bare Month',
                            'per_trip' => 'Per Trip',
                        ];

                        return $labels[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('estimated_discount')
                    ->numeric()
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->money('PHP')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('PHP'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->before(function (array $data, RelationManager $livewire) {
                        $rent = $livewire->getOwnerRecord();

                        // Check if this equipment is already added to the rent
                        $equipmentExists = $rent->rentItems()
                            ->where('equipment_id', $data['equipment_id'])
                            ->exists();

                        if ($equipmentExists) {
                            throw new \Exception('This equipment is already added to the rent.');
                        }
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        $quantity = floatval($data['quantity'] ?? 1);
                        $rate = floatval($data['rate'] ?? 0);
                        $discount = floatval($data['estimated_discount'] ?? 0);

                        if (!isset($data['equipment_id'])) {
                            throw new \Exception('Equipment must be selected');
                        }

                        if (isset($data['rent_type'])) {
                            $data['rent_type'] = (string) $data['rent_type'];
                        }

                        $data['total_amount'] = ($rate * $quantity) - $discount;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->before(function (array $data, $record, RelationManager $livewire) {
                        $rent = $livewire->getOwnerRecord();

                        // Check if this equipment is already added to the rent (excluding the current record)
                        $equipmentExists = $rent->rentItems()
                            ->where('equipment_id', $data['equipment_id'])
                            ->where('id', '!=', $record->id)
                            ->exists();

                        if ($equipmentExists) {
                            throw new \Exception('This equipment is already added to the rent.');
                        }
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        $quantity = floatval($data['quantity'] ?? 1);
                        $rate = floatval($data['rate'] ?? 0);
                        $discount = floatval($data['estimated_discount'] ?? 0);

                        if (!isset($data['equipment_id'])) {
                            throw new \Exception('Equipment must be selected');
                        }

                        if (isset($data['rent_type'])) {
                            $data['rent_type'] = (string) $data['rent_type'];
                        }

                        $data['total_amount'] = ($rate * $quantity) - $discount;

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
