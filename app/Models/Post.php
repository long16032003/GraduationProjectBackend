<?php

namespace App\Models;

use App\Models\ModelFilters\PostFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use Filterable;
    protected $table = 'posts';
    public $timestamps = true;

    protected $fillable = [
        'creator_id',
        'title',
        'summary',
        'content',
    ];

    protected $attributes = [
        'creator_id' => 0,
        'title' => '',
        'summary' => '',
        'content' => '',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(PostFilter::class);
    }
}
