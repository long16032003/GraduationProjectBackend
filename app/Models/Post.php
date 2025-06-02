<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Post extends Model
{
    protected $table = 'posts';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'summary',
        'content',
    ];

    protected $attributes = [
        'title' => '',
        'summary' => '',
        'content' => '',
    ];
}
