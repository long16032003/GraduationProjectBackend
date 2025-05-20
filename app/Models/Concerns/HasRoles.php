<?php

namespace App\Models\Concerns;

use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function dynamicRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole(string|array|Collection|BackedEnum $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if ($roles instanceof BackedEnum) {
            $roles = [$roles->value];
        }

        return $this->roles->whereIn('key', collect($roles)->filter())->isNotEmpty();
    }

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
