<?php

namespace App\Models;

use App\Models\Concerns\HasPermissions;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasPermissions;
    protected function casts(): array
    {
        return [
            'permissions' => 'collection'
        ];
    }

    protected $attributes = [
        'permissions' => '[]',
    ];
}
