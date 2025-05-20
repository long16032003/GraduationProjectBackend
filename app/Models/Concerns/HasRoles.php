<?php

namespace App\Models\Concerns;

use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    use HasPermissions;

    public function isSuperAdmin(): bool
    {
        return (bool) ($this->superadmin ?? false);
    }

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
}
