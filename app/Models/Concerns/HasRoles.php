<?php

namespace App\Models\Concerns;

use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function isSuperAdmin(): bool
    {
        return (bool) ($this->superadmin ?? false);
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function filterRoles(string|array|Collection|Role|BackedEnum $roles): Collection
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if ($roles instanceof BackedEnum) {
            $roles = [$roles->value];
        }

        if ($roles instanceof Collection) {
            $pivot = $roles->filter(fn ($role) => $role instanceof Role);

            if ($pivot->isNotEmpty()) {
                $roles = $pivot->map(fn ($role) => $role->key);
            }
        }

        return $this->roles->whereIn('role', collect($roles)->filter());
    }

    public function viaRolePermissions(string|array|Collection|Role|BackedEnum $roles): Collection
    {
        return $this->filterRoles($roles)
            ->map(fn (Role $role) => $role->permissions)
            ->flatten()
            ->mapWithKeys(fn (string $permission) => [$permission => 1]);
    }

    public function hasRolePermission(
        string|array|Collection|BackedEnum $roles,
        string|array|Collection|BackedEnum $permissions,
        bool $hasAny = true
    ): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        if ($permissions instanceof BackedEnum) {
            $permissions = [$permissions->value];
        }

        $viaRolePermissions = $this->viaRolePermissions($roles);

        if ($viaRolePermissions->has('*')) {
            return true;
        }

        return $hasAny
            ? $viaRolePermissions->hasAny($permissions)
            : $viaRolePermissions->has($permissions);
    }

    public function hasPermission(string|array|Collection|BackedEnum $permissions, bool $hasAny = true): bool
    {
        // superadmin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->hasRolePermission($this->roles, $permissions, $hasAny);
    }
    public function hasAllPermission(string|array|Collection|BackedEnum $permissions): bool
    {
        return $this->hasPermission($permissions, false);
    }
}
