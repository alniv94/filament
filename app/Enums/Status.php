<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum Status: string implements HasLabel, HasColor
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }
}
