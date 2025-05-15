<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum RentItemStatus: string implements HasLabel, HasColor
{
    case OPEN = 'Open';
    case ON_TRANSIT = 'On Transit';
    case ARRIVED = 'Arrived';



    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'blue',
            self::ON_TRANSIT => 'info',
            self::ARRIVED => 'success',
        };
    }
}
