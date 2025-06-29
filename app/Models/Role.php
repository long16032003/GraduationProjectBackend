<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property array $permissions
 */
class Role extends Model
{
    protected $fillable = [
        'name',
        'level',
        'status',
        'permissions'
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'status' => 'boolean'
        ];
    }

    protected $attributes = [
        'permissions' => '[]',
        'level' => 0,
        'status' => true,
    ];

    /**
     * Get all of the users that are assigned this role.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }
}
