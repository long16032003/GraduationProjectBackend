<?php

namespace App\Concerns;

use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ladder\Models\UserRole;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string|array|Collection|BackedEnum $roles): bool
    {
        return $this->filterRoles($roles)->isNotEmpty();
    }

    public function rolePermissions(string|array|Collection|BackedEnum $roles): array
    {
        return $this->filterRoles($roles)
            ->map(fn (Role $role) => $role->permissions)
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function filterRoles(string|array|Collection|BackedEnum $roles): \Illuminate\Database\Eloquent\Collection
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if ($roles instanceof BackedEnum) {
            $roles = [$roles->value];
        }

        return $this->roles->whereIn('key', collect($roles)->filter());
    }

    public function hasRolePermission(string|array|Collection|BackedEnum $roles,
                                      string|array|Collection|BackedEnum $permissions): bool
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
        return $this->hasRolePermission($this->roles, $permissions);
    }

    public function permissions(): Collection
    {
        return collect($this->rolePermissions($this->roles));
    }
}
