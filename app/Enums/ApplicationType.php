<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ApplicationType: string implements HasColor, HasLabel
{
    case TE = 'Telecom Exchange';
    case PGCC = 'Power Grid Control Center';
    case SCADA = 'SCADA System';
    case SAAS = 'SaaS';
    case DESKTOP = 'Desktop';
    case SERVER = 'Server';
    case APPLIANCE = 'Appliance';
    case OTHER = 'Other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TE => 'Telecom Exchange',
            self::PGCC => 'Power Grid Control Center',
            self::SCADA => 'SCADA System',
            self::SAAS => 'SaaS',
            self::DESKTOP => 'Desktop',
            self::SERVER => 'Server',
            self::APPLIANCE => 'Appliance',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TE => 'primary',
            self::PGCC => 'primary',
            self::SCADA => 'primary',
            self::SAAS => 'primary',
            self::DESKTOP => 'info',
            self::SERVER => 'success',
            self::APPLIANCE => 'warning',
            self::OTHER => 'secondary',
        };
    }
}
