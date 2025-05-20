<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property array $permissions
 */
class Role extends Model
{
    protected function casts(): array
    {
        return [
            'permissions' => 'array'
        ];
    }

    protected $attributes = [
        'permissions' => '[]',
    ];

    /**
     * Get all of the users that are assigned this role.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }
}
