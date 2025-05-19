<?php

namespace App;

class PermissionManager
{
    public static array $permissions = [];

    public static function hasPermissions(): bool
    {
        return count(static::$permissions) > 0;
    }

    public static function permissions(array $permissions): static
    {
        static::$permissions = $permissions;

        return new static;
    }
}
