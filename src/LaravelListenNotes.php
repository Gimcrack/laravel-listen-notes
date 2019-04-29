<?php

namespace Ingenious\LaravelListenNotes;


use Ingenious\LaravelListenNotes\Contracts\PodcastProvider;
use Ingenious\LaravelListenNotes\Exceptions\InvalidPodcastException;
use Ingenious\LaravelListenNotes\Models\Genre;
use Ingenious\LaravelListenNotes\Repositories\ApiResponse;
use Ingenious\LaravelListenNotes\Repositories\Episode;
use Ingenious\LaravelListenNotes\Repositories\EpisodeCollection;
use Ingenious\LaravelListenNotes\Repositories\GenreCollection;
use Ingenious\LaravelListenNotes\Repositories\Podcast;
use Ingenious\LaravelListenNotes\Repositories\PodcastCollection;
use Ingenious\LaravelListenNotes\Repositories\SearchResults;

class LaravelListenNotes extends BasePodcastProvider implements PodcastProvider
{

    protected const CACHE_ONE_MONTH = 30 * 24 * 60 * 60;
    protected const CACHE_ONE_WEEK = 7 * 24 * 60 * 60;
    protected const CACHE_ONE_DAY = 24 * 60 * 60;
    protected const CACHE_ONE_HOUR = 60 * 60;

    /**
     * Search Podcasts
     *
     * @param $query
     * @param array $options
     * @return \Ingenious\LaravelListenNotes\Repositories\PodcastCollection
     */
    public function searchPodcasts($query, $options = []) : PodcastCollection
    {
        return $this->search($query, "podcast", $options)->podcasts();
    }

    /**
     * Search episodes
     *
     * @param $query
     * @param array $options
     * @return \Ingenious\LaravelListenNotes\Repositories\EpisodeCollection
     */
    public function searchEpisodes($query, $options = []) : EpisodeCollection
    {
        return $this->search($query, "episode", $options)->episodes();
    }

    /**
     * Search the podcasts
     *
     * @param $query
     * @param array $options
     * @return array
     */
    public function searchPodcastsRaw($query, $options = []) : array
    {
        return $this->get("search", array_merge([
            'q' => $query,
            'type' => "podcast",
            'language' => 'English'
        ], $options))->toArray();
    }

    /**
     * Search the episodes
     *
     * @param $query
     * @param array $options
     * @return array
     */
    public function searchEpisodesRaw($query, $options = []) : array
    {
        return $this->get("search", array_merge([
            'q' => $query,
            'type' => "episode",
            'language' => 'English'
        ], $options))->toArray();
    }

    //public function searchCurated($query)
    //{
    //    return $this->search($query, "curated");
    //}

    /**
     * Get the podcast by name
     *
     * @param $name
     * @return \Ingenious\LaravelListenNotes\Repositories\Podcast
     * @throws \Ingenious\LaravelListenNotes\Exceptions\InvalidPodcastException
     */
    public function podcastByName($name) : Podcast
    {
        $result = collect($this->searchPodcastsRaw($name)['results'])
            ->filter( function($row) use ($name) {
                return $row['title_original'] === $name;
            })
            ->first();

        if ( ! $result )
            throw new InvalidPodcastException("Cannot find podcast with {$name}");

        //$result = $this->searchPodcasts($name)->first();

        return Podcast::find($result['id']);
    }

    /**
     * Get the specified podcast
     *
     * @param $id
     * @param null $next_episode_pub_date
     * @param string $sort recent_first|oldest_first
     * @return \Ingenious\LaravelListenNotes\Repositories\Podcast
     */
    public function podcast($id, $next_episode_pub_date = null, $sort = "recent_first") : Podcast
    {
        return $this->podcastMeta($id, $next_episode_pub_date, $sort)->podcast();
    }

    /**
     * Get the podcast in Raw ApiResponse form
     *
     * @param $id
     * @param null $next_episode_pub_date
     * @param string $sort
     * @return \Ingenious\LaravelListenNotes\Repositories\ApiResponse
     */
    public function podcastMeta($id, $next_episode_pub_date = null, $sort = "recent_first") : ApiResponse
    {
        $params = compact('sort');

        if ( $next_episode_pub_date )
            $params['next_episode_pub_date'] = $next_episode_pub_date;

        return $this->get("podcasts/{$id}", $params);
    }

    /**
     * Get the episode
     *
     * @param $id
     * @return \Ingenious\LaravelListenNotes\Repositories\Episode
     */
    public function episode($id) : Episode
    {
        return $this->episodeMeta($id)->episode();
    }

    /**
     * Get the episode in Raw ApiResponse form
     *
     * @param $id
     * @return \Ingenious\LaravelListenNotes\Repositories\ApiResponse
     */
    public function episodeMeta($id) : ApiResponse
    {
        return $this->ttl(static::CACHE_ONE_MONTH)->get("episodes/{$id}");
    }

    /**
     * Get the recommendations for the podcast
     *
     * @param $podcast_id
     * @return \Ingenious\LaravelListenNotes\Repositories\PodcastCollection
     */
    public function recommendations($podcast_id) : PodcastCollection
    {
        return $this->ttl(static::CACHE_ONE_WEEK)->get("podcasts/{$podcast_id}/recommendations")->recommendations();
    }

    /**
     * Get episodes for the specified Podcast
     *
     * @param \Ingenious\LaravelListenNotes\Repositories\Podcast $podcast
     * @return \Ingenious\LaravelListenNotes\Repositories\EpisodeCollection
     */
    public function episodes(Podcast $podcast) : EpisodeCollection
    {
        return $this->podcastMeta($podcast->id)->episodes();
    }

    /**
     * Get the collection of genres
     *
     * @return \Ingenious\LaravelListenNotes\Repositories\GenreCollection
     */
    public function genres() : GenreCollection
    {
        return $this->genresMeta()->genres();
    }

    /**
     * Get the raw genre data
     *
     * @return \Ingenious\LaravelListenNotes\Repositories\ApiResponse
     */
    public function genresMeta() : ApiResponse
    {
        return $this->ttl(static::CACHE_ONE_MONTH)->get("genres");
    }

    /**
     * Get the curated list of podcasts
     *
     * @return \Ingenious\LaravelListenNotes\Repositories\PodcastCollection
     */
    public function curated() : PodcastCollection
    {
        $list = collect(config('laravel-listen-notes.best-podcasts'))
            ->transform( function($name) {
                try {
                    return static::podcastByName($name);
                }
                catch(InvalidPodcastException $e)
                {
                    //\Log::info("Cant find {$name}");
                    return null;
                }
            })
            ->filter()
            ->values()
            ->all();

        return new PodcastCollection($list);
    }

    /**
     * Get the best podcasts for the specified genre
     *
     * @param $genre_id
     * @param int $page
     * @return \Ingenious\LaravelListenNotes\Repositories\PodcastCollection
     */
    public function bestPodcasts($genre_id = null, $page = 1) : PodcastCollection
    {
        return $this->bestPodcastsMeta($genre_id, $page)->bestPodcasts();
    }

    /**
     * Get the raw best podcasts data for the specified genre
     *
     * @param $genre_id
     * @param int $page
     * @return \Ingenious\LaravelListenNotes\Repositories\ApiResponse
     */
    public function bestPodcastsMeta($genre_id = null, $page = 1) : ApiResponse
    {
        if ($genre_id)
            return $this->ttl(static::CACHE_ONE_WEEK)->get("best_podcasts", compact('genre_id', 'page'));

        return $this->ttl(static::CACHE_ONE_WEEK)->get("best_podcasts", compact('page'));
    }

    /**
     * Perform a search
     *
     * @param $query
     * @param $type
     * @param array $options
     * @return \Ingenious\LaravelListenNotes\Repositories\SearchResults
     */
    protected function search($query, $type, $options = []) : SearchResults
    {
        return $this->get("search", array_merge([
                'q' => $query,
                'type' => $type,
                'language' => 'English'
            ], $options))->results();

    }
}