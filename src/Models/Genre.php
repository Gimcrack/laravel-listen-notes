<?php

namespace Ingenious\LaravelListenNotes\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];

    /**
     * A Genre can have a parent Genre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Genre::class);
    }
}
