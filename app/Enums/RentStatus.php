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
    case VOID = 'Void';


    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::SUBMITTED => 'warning',
            self::POSTED => 'success',
            self::CANCELLED => 'danger',
            self::VOID => 'gray',
        };
    }
}
