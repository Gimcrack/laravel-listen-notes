<?php

namespace Ingenious\LaravelListenNotes;

use Zttp\Zttp;

class LaravelListenNotes {

    protected $endpoint;

    protected $token;

    protected $force_flag;

    /**
     * New up a new LaravelListenNotes class
     */
    public function __construct()
    {
        $this->endpoint = config('laravel-listen-notes.endpoint');

        $this->token = config('laravel-listen-notes.token');

        $this->force_flag = false;
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
            trim($this->endpoint,'/'),
            trim($url,'/')
        ]);

        return $url;
    }

    public function force()
    {
        $this->force_flag = true;

        return $this;
    }

    /**
     * Get the request url and return json
     * @method getJson
     *
     * @param  string $url
     * @param  array|mixed $params
     * @return   string
     */
    public function get($url, $params)
    {
        $expanded = $this->url($url);

        if ( $this->force_flag )
        {
            Cache::forget("laravel-listen-notes.{$expanded}");
        }

        $this->force_flag = false;

        //return Cache::remember( "laravel-listen-notes.{$expanded}", 15, function() use ($expanded, $params) {
            return Zttp::withHeaders(['X-ListenAPI-Key' => $this->token])->get($expanded, $params)->json();
        //});
    }

    public function search($query, $type = "episode")
    {
        return $this->get("search", [
            'q' => $query,
            'type' => $type
        ]);
    }

    public function searchPodcasts($query)
    {
        return $this->search($query, "podcast");
    }

    public function searchCurated($query)
    {
        return $this->search($query, "curated");
    }
}