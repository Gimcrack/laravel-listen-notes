<?php

namespace Ingenious\LaravelListenNotes;

use Zttp\Zttp;

class LaravelListenNotes {

    protected $endpoint;

    /**
     * New up a new LaravelListenNotes class
     */
    public function __construct()
    {
        $this->endpoint = "endpoint";
    }

    /**
     * Get the endpoint
     * @method endpoint
     *
     * @return   string
     */
    protected function endpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the formatted url
     * @method url
     *
     * @param  string $url
     * @return   string
     */
    protected function url($url)
    {
        $url = vsprintf("%s/%s", [
            $this->endpoint,
            trim($url,'/')
        ]);

        return $url;
    }

    /**
     * Get the request url and return json
     * @method getJson
     *
     * @param  string  $url
     * @return   string
     */
    public function get($url)
    {
        $expanded = $this->url($url);

        if ( $this->force_flag )
        {
            Cache::forget("laravel-listen-notes.{$expanded}");
        }

        $this->force_flag = false;

        return Cache::remember( "laravel-listen-notes.{$expanded}", 15, function() use ($expanded) {
            return Zttp::get($expanded)->json();
        });
    }
}
