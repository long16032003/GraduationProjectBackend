<?php

namespace App\Concerns;

use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\UserRole;

trait HasRoles
{
    public function dynamicRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole(string|array|Collection|BackedEnum $roles): bool
    {
        return $this->filterRoles($roles)->isNotEmpty();
    }

    public function hasRolePermission(
        string|array|Collection|BackedEnum $roles,
        string|array|Collection|BackedEnum $permissions
    ): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        if ($permissions instanceof BackedEnum) {
            $permissions = [$permissions->value];
        }

        $rolePermissions = collect($this->rolePermissions($roles));

        $permissions = collect($permissions);

        return $permissions->contains(fn ($permission) =>
            $rolePermissions->contains($permission) ||
            $rolePermissions->contains('*') ||
            (Str::endsWith($permission, ':create') && $rolePermissions->contains('*:create')) ||
            (Str::endsWith($permission, ':update') && $rolePermissions->contains('*:update'))
        );
    }

    public function hasPermission(string|array|Collection|BackedEnum $permissions): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        if ($permissions instanceof BackedEnum) {
            $permissions = [$permissions->value];
        }

        return $this->hasRolePermission($this->roles, $permissions);
    }
}
