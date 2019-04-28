<?php

namespace Ingenious\LaravelListenNotes;

use Ingenious\LaravelListenNotes\Concerns\MakesHttpRequests;

abstract class BasePodcastProvider
{
    use MakesHttpRequests;

    /**
     * New up a new LaravelListenNotes class
     */
    public function __construct()
    {
        $this->endpoint = config('laravel-listen-notes.endpoint');

        $this->token = config('laravel-listen-notes.token');

        $this->force_flag = false;
    }
}