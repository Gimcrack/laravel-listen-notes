<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use Illuminate\Support\Collection;
use Ingenious\LaravelListenNotes\Concerns\CollectionOverrides;

class EpisodeCollection extends Collection
{
    use CollectionOverrides;

    public static function collect($items, $podcast = null)
    {
        return (new static($items))
            ->transform(function($row) use ($podcast) {
                return new Episode($row, $podcast);
            });
    }
}