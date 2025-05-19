<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
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
