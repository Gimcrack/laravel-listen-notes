<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use Illuminate\Support\Collection;
use Ingenious\LaravelListenNotes\Concerns\CollectionOverrides;

class GenreCollection extends Collection
{
    use CollectionOverrides;

    public static function collect($items)
    {
        return (new static($items))
            ->transform(function($row) {
                return new Genre($row);
            });
    }
}