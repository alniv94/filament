<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentResource\Pages;
use App\Filament\Resources\RentResource\RelationManagers;
use App\Models\Rent;
use App\Models\Equipment;
use App\Models\RentItem;
use App\Enums\RentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;

class RentResource extends Resource
{
    protected static ?string $model = Rent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getFormSchema());
    }

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('erf_number')
                ->required(),
            Forms\Components\DatePicker::make('erf_date')
                ->required(),
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->preload()
                ->required()
                ->columnSpanFull()
                ->searchable(),
            Forms\Components\DatePicker::make('departure_date')
                ->required(),
            Forms\Components\DatePicker::make('arrival_date')
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    RentStatus::OPEN->value => RentStatus::OPEN->getLabel(),
                ])
                ->default(RentStatus::OPEN->value)
                ->disabled()
                ->dehydrated()
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('erf_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departure_date')
                    ->searchable()
                    ->date(),
                Tables\Columns\TextColumn::make('arrival_date')
                    ->searchable()
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state): string => RentStatus::tryFrom($state)?->getColor() ?? 'gray')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->color('gray')
                    ->button()
                    ->visible(
                        fn(Rent $record): bool =>
                        $record->status === RentStatus::OPEN->value ||
                            $record->status === RentStatus::SUBMITTED->value
                    )
                    ->action(function (Rent $record) {
                        $oldStatus = $record->status;
                        $newStatus = RentStatus::VOID->value;

                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title('Rent status updated')
                            ->body("Status changed from {$oldStatus} to {$newStatus}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->color('warning')
                    ->button()
                    ->visible(
                        fn(Rent $record): bool =>
                        $record->status === RentStatus::OPEN->value
                    )
                    ->action(function (Rent $record) {
                        $oldStatus = $record->status;
                        $newStatus = RentStatus::SUBMITTED->value;

                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title('Rent status updated')
                            ->body("Status changed from {$oldStatus} to {$newStatus}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('post')
                    ->label('Post')
                    ->color('success')
                    ->button()
                    ->visible(
                        fn(Rent $record): bool =>
                        $record->status === RentStatus::SUBMITTED->value
                    )
                    ->action(function (Rent $record) {
                        $oldStatus = $record->status;
                        $newStatus = RentStatus::POSTED->value;

                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title('Rent status updated')
                            ->body("Status changed from {$oldStatus} to {$newStatus}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->button()
                    ->visible(
                        fn(Rent $record): bool =>
                        $record->status === RentStatus::POSTED->value
                    )
                    ->action(function (Rent $record) {
                        $oldStatus = $record->status;
                        $newStatus = RentStatus::CANCELLED->value;

                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title('Rent status updated')
                            ->body("Status changed from {$oldStatus} to {$newStatus}")
                            ->success()
                            ->send();
                    }),

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

    public static function getRelations(): array
    {
        return [
            RelationManagers\RentItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRents::route('/'),
            'create' => Pages\CreateRent::route('/create'),
            'view' => Pages\ViewRent::route('/{record}'),
            'edit' => Pages\EditRent::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Rent Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('erf_number')
                            ->label('ERF Number'),
                        Infolists\Components\TextEntry::make('erf_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn($state): string => RentStatus::tryFrom($state)?->getColor() ?? 'gray'),
                    ])->columns(3),

                Infolists\Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer.name')
                            ->label('Customer'),
                    ]),

                Infolists\Components\Section::make('Scheduling')
                    ->schema([
                        Infolists\Components\TextEntry::make('departure_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('arrival_date')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Additional Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->markdown(),
                    ]),

                Infolists\Components\Section::make('Financial Summary')
                    ->schema([
                        Infolists\Components\TextEntry::make('rentItems.rate')
                            ->state(function ($record) {
                                $total = $record->rentItems->sum('total_amount');
                                return number_format($total, 2);
                            })
                            ->label('Total Amount')
                            ->money('PHP')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    ]),
            ]);
    }
}
