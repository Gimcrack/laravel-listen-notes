<?php

namespace Ingenious\LaravelListenNotes\Models;

use function array_reverse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Genre extends Model
{
    protected $guarded = [];

    protected $appends = [
        'slug',
        'url'
    ];

    public function getSlugAttribute()
    {
        return $this->id . "-" . Str::kebab( str_replace('&','',$this->name) );
    }

    public function getUrlAttribute()
    {
        $ancestors = [$this->slug];

        if ( $this->parent )
        {
            $ancestors[] = $this->parent->slug;

            if ( $this->parent->parent )
            {
                $ancestors[] = $this->parent->parent->slug;

                if ( $this->parent->parent->parent )
                {
                    $ancestors[] = $this->parent->parent->parent->slug;

                    if ( $this->parent->parent->parent->parent )
                    {
                        $ancestors[] = $this->parent->parent->parent->parent->slug;
                    }
                }
            }
        }

        array_pop($ancestors);

        return implode("/", array_reverse($ancestors) );
    }

    /**
     * A Genre can have a parent Genre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * A Genre can have many child Genres
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Genre::class,'parent_id')->orderBy('name');
    }
}
