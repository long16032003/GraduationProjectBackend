<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
