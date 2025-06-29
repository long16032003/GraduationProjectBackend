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

    public function permissions(): Collection
    {
        return $this->viaRolePermissions();
    }

    public function filterRoles(string|array|Collection|Role|BackedEnum $roles = []): Collection
    {
        if (empty($roles)) {
            return $this->roles;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if ($roles instanceof BackedEnum) {
            $roles = [$roles->value];
        }

        if ($roles instanceof Collection) {
            $pivot = $roles->filter(fn ($role) => $role instanceof Role);

            if ($pivot->isNotEmpty()) {
                $roles = $pivot->map(fn ($role) => $role->name);
            }
        }

        return $this->roles->whereIn('name', collect($roles)->filter());
    }

    public function viaRolePermissions(string|array|Collection|Role|BackedEnum $roles = []): Collection
    {
        return $this->filterRoles($roles)
            ->map(fn (Role $role) => collect($role->permissions)->keys())
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

    /**
     * Assign a role to the user
     */
    public function assignRole(string|int|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        } elseif (is_int($role)) {
            $role = Role::findOrFail($role);
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
        return $this;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string|int|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('key', $role)->first();
        } elseif (is_int($role)) {
            $role = Role::find($role);
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }

    /**
     * Sync user roles (replace all)
     */
    public function syncRoles(array $roles): self
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if (is_string($role)) {
                $roleModel = Role::where('key', $role)->first();
                if ($roleModel) $roleIds[] = $roleModel->id;
            } elseif (is_int($role)) {
                $roleIds[] = $role;
            } elseif ($role instanceof Role) {
                $roleIds[] = $role->id;
            }
        }

        $this->roles()->sync($roleIds);
        return $this;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string|int|Role $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('key', $role);
        } elseif (is_int($role)) {
            return $this->roles->contains('id', $role);
        } elseif ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }
        return false;
    }
}
