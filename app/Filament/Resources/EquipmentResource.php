<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\Status;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';


    public static function getNavigationGroup(): ?string
    {
        return 'Equipment';
    }

    public static function getNavigationLabel(): string
    {
        return 'Manage Equipment';
    }


    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('equipment_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('plate_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('model_name')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('date_purchased'),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->prefix('₱'),
                Forms\Components\DateTimePicker::make('last_maintenance_date'),
                Forms\Components\DateTimePicker::make('next_maintenance_date')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            $equipment = new Equipment();
                            $equipment->next_maintenance_date = $state;
                            $remainingDays = $equipment->calculateRemainingDays();
                            $set('remaining_days_for_maintenance', $remainingDays);
                        }
                    }),
                Forms\Components\TextInput::make('remaining_days_for_maintenance')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('fuel_consumption_number')
                    ->numeric(),
                Forms\Components\TextInput::make('size_number')
                    ->numeric(),
                Forms\Components\TextInput::make('capacity_max')
                    ->numeric(),
                Forms\Components\TextInput::make('capacity_tip')
                    ->numeric(),
                Forms\Components\TextInput::make('acel_rate_dry')
                    ->numeric(),
                Forms\Components\TextInput::make('acel_rate_hour')
                    ->numeric(),
                Forms\Components\TextInput::make('nsjbi_rate_dry')
                    ->numeric(),
                Forms\Components\TextInput::make('nsjbi_rate_hour')
                    ->numeric(),
                Forms\Components\TextInput::make('bare_month')
                    ->numeric(),
                Forms\Components\TextInput::make('per_trip')
                    ->numeric(),
                Forms\Components\TextInput::make('est_repair_cost')
                    ->maxLength(15),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date_issued'),
                Forms\Components\Select::make('status')
                    ->options(Status::class)
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\FileUpload::make('equipment_image')
                    ->columnSpanFull()
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->directory('equipment_images')
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipment_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_purchased')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('php')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_maintenance_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('next_maintenance_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_days_for_maintenance')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state < 0 => 'danger',
                        $state <= 7 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match (true) {
                        $state < 0 => 'Overdue by ' . abs($state) . ' days',
                        $state == 0 => 'Due today',
                        default => $state . ' days remaining',
                    }),
                Tables\Columns\TextColumn::make('fuel_consumption_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_max')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_tip')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('acel_rate_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('acel_rate_hour')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nsjbi_rate_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nsjbi_rate_hour')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bare_month')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('per_trip')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('est_repair_cost')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_issued')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('maintenance_status')
                    ->options([
                        'overdue' => 'Maintenance Overdue',
                        'due_soon' => 'Due Within 7 Days',
                        'upcoming' => 'Upcoming (8-30 days)',
                        'future' => 'Future (30+ days)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'overdue' => $query->whereRaw('DATEDIFF(next_maintenance_date, CURDATE()) < 0'),
                            'due_soon' => $query->whereRaw('DATEDIFF(next_maintenance_date, CURDATE()) BETWEEN 0 AND 7'),
                            'upcoming' => $query->whereRaw('DATEDIFF(next_maintenance_date, CURDATE()) BETWEEN 8 AND 30'),
                            'future' => $query->whereRaw('DATEDIFF(next_maintenance_date, CURDATE()) > 30'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEquipment::route('/'),
            'view' => Pages\ViewEquipment::route('/{record}'),
        ];
    }
}
