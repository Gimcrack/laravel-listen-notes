<?php

namespace Ingenious\LaravelListenNotes\Models;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string'
    ];
}
