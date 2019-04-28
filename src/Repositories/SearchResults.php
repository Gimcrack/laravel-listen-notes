<?php

namespace Ingenious\LaravelListenNotes\Repositories;

class SearchResults
{
    protected $_results;

    public static function collect($results)
    {
        return new static($results);
    }

    public function __construct($results)
    {
        $this->_results = $results;
    }

    public function __get($key)
    {
        $alternate = $key . "_original";
        return $this->_results->$key ?: $this->_results->$alternate;
    }

    public function results()
    {
        return $this->_results;
    }

    public function podcasts()
    {
        return PodcastCollection::collect($this->results());
    }

    public function episodes()
    {
        return EpisodeCollection::collect($this->results());
    }
}