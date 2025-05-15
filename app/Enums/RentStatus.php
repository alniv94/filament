<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum RentStatus: string implements HasLabel, HasColor
{
    case OPEN = 'Open';
    case SUBMITTED = 'Submitted';
    case POSTED = 'Posted';
    case CANCELLED = 'Cancelled';


    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'blue',
            self::SUBMITTED => 'info',
            self::POSTED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
