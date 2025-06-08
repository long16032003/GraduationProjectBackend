<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    public $timestamps = true;

    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'title',
        'path',
        'type',
        'size',
        'created_at',
        'updated_at'
    ];
}
