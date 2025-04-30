<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewEquipment extends ViewRecord
{
    protected static string $resource = EquipmentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Equipment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('equipment_number'),
                        Infolists\Components\TextEntry::make('plate_number'),
                        Infolists\Components\TextEntry::make('model_name'),
                        Infolists\Components\TextEntry::make('brand.name'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->markdown(),
                    ]),

                Infolists\Components\Section::make('Financial Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('cost')
                            ->money('php'),
                        Infolists\Components\TextEntry::make('date_purchased')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('est_repair_cost'),
                    ])->columns(3),

                Infolists\Components\Section::make('Maintenance Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('last_maintenance_date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('next_maintenance_date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('remaining_days_for_maintenance')
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
                    ])->columns(3),

                Infolists\Components\Section::make('Equipment Images')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('equipment_image')
                                    ->height(300)
                                    ->visibility('public')
                                    ->extraImgAttributes(['class' => 'object-contain']),
                            ]),
                    ]),

            ]);
    }
}
