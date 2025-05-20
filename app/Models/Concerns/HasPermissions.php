<?php

namespace App\Models\Concerns;

use BackedEnum;
use Illuminate\Support\Collection;

trait HasPermissions
{
    public function hasPermission(string|array|Collection|BackedEnum $permissions, bool $isAll = false): bool
    {
        if ($this->permissions->has('*')) {
            return true;
        }

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        if ($permissions instanceof BackedEnum) {
            $permissions = [$permissions->value];
        }

        return $isAll
            ? $this->permissions->has($permissions)
            : $this->permissions->hasAny($permissions);
    }

    public function hasAllPermission(string|array|Collection|BackedEnum $permissions): bool
    {
        return $this->hasPermission($permissions, true);
    }
}
