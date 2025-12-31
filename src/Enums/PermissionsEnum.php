<?php

namespace NuxtIt\RP\Enums;


enum PermissionsEnum: string
{
    // Dashboard Permissions
    case RP_MANAGEMENT = 'rp-management';

    /**
     * Get all permission values as an array
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get permission value
     */
    public function value(): string
    {
        return $this->value;
    }
}
