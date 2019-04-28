<?php

namespace Ingenious\LaravelListenNotes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ingenious\LaravelListenNotes\LaravelListenNotes
 */
class LaravelListenNotes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'PodcastSearch';
    }
}
