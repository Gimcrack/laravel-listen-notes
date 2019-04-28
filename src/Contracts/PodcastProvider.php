<?php

namespace Ingenious\LaravelListenNotes\Contracts;


use Ingenious\LaravelListenNotes\Repositories\ApiResponse;
use Ingenious\LaravelListenNotes\Repositories\Episode;
use Ingenious\LaravelListenNotes\Repositories\EpisodeCollection;
use Ingenious\LaravelListenNotes\Repositories\GenreCollection;
use Ingenious\LaravelListenNotes\Repositories\Podcast;
use Ingenious\LaravelListenNotes\Repositories\PodcastCollection;

interface PodcastProvider
{
    public function searchPodcasts($query, $options) : PodcastCollection;

    public function searchEpisodes($query, $options) : EpisodeCollection;

    public function podcast($id, $next_episode_pub_date, $sort) : Podcast;

    public function podcastMeta($id, $next_episode_pub_date, $sort) : ApiResponse;

    public function podcastByName($name) : Podcast;

    public function episodes(Podcast $podcast) : EpisodeCollection;

    public function episode($id) : Episode;

    public function episodeMeta($id) : ApiResponse;

    public function recommendations($podcast_id) : PodcastCollection;

    public function genres() : GenreCollection;

    public function genresMeta() : ApiResponse;

    public function bestPodcasts($genre_id, $page) : PodcastCollection;

    public function bestPodcastsMeta($genre_id, $page) : ApiResponse;
}