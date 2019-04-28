<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use function property_exists;

class ApiResponse
{
    protected $_json;

    public function __construct($response)
    {
        $this->_json = (object) $response;
    }

    public function __get($key)
    {
        if ( property_exists($this->json(), $key))
            return $this->json()->$key;

        return null;
    }

    public function results() : SearchResults
    {
        return SearchResults::collect($this->json()->results);
    }

    public function podcast() : Podcast
    {
        return new Podcast($this->json());
    }

    public function episode() : Episode
    {
        return new Episode($this->json());
    }

    public function episodes($podcast = null) : EpisodeCollection
    {
        return EpisodeCollection::collect( $this->json()->episodes, $podcast );
    }

    public function podcasts() : PodcastCollection
    {
        return PodcastCollection::collect( $this->json() );
    }

    public function recommendations() : PodcastCollection
    {
        return PodcastCollection::collect( $this->json()->recommendations );
    }

    public function genres() : GenreCollection
    {
        return GenreCollection::collect( $this->json()->genres );
    }

    public function bestPodcasts() : PodcastCollection
    {
        return PodcastCollection::collect($this->json()->channels);
    }

    public function json()
    {
        return $this->_json;
    }

    public function toArray()
    {
        return (array) $this->json();
    }
}