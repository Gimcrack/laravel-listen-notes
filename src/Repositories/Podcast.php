<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use Carbon\Carbon;
use function get_object_vars;
use Ingenious\LaravelListenNotes\Concerns\Jsonable;
use Ingenious\LaravelListenNotes\Exceptions\InvalidPodcastException;
use Ingenious\LaravelListenNotes\Exceptions\NotFoundException;
use Ingenious\LaravelListenNotes\Models\Podcast as PodcastModel;
use function is_string;
use function property_exists;

/**
 * @property  string id
 * @property int|null total_episodes
 */
class Podcast
{
    use Jsonable;

    protected $_episodes;

    protected $_next_episode_fetch_date;

    protected $model;

    public static function find($id)
    {
        if ($model = PodcastModel::find($id) )
            return new static($model);

        return new static(compact('id'));
    }

    public function __construct($meta)
    {
        if ( is_object($meta) && get_class($meta) === PodcastModel::class) {
            $this->model = $meta;
            $this->_meta = (object) [];

            $this->hydrate();
        }

        else {
            $this->_meta = (object) $meta;
            $this->model = new PodcastModel;

            $this->persist();
        }


        $this->_episodes = new EpisodeCollection;

        $this->_next_episode_fetch_date = null;

        $this->hydrate();
    }

    public function __get($key)
    {
        if( property_exists($this->_meta, $key))
            return $this->_meta->$key;

        if( property_exists($this->_meta, $alternate = $key . "_original"))
            return $this->_meta->$alternate;

        return $this->model->$key;
    }

    public function hydrate()
    {
        $this->_meta = $this->fetch()->json();
    }

    public function persist()
    {
        $this->model = PodcastModel::firstOrCreate([
            'id' => $this->id,
        ], [
            'latest_pub_date' => Carbon::parse($this->latest_pub_date_ms / 1000)
        ]);

        return $this->model;
    }

    public function model()
    {
        return $this->model ?? $this->persist();
    }

    public function fetchEpisodes($next_episode_pub_date = null)
    {
        if ( $this->_episodes->count() === $this->total_episodes )
            return $this;

        $meta = $this->fetch($next_episode_pub_date ?? $this->_next_episode_fetch_date);

        $this->_next_episode_fetch_date = $meta->next_episode_pub_date;

        $this->_episodes = $this->_episodes->merge(
            $meta->episodes($podcast = $this)
        );

        return $this;
    }

    public function fetch($next_episode_pub_date = null) : ApiResponse
    {
        try {
            return app('PodcastSearch')->podcastMeta($this->id, $next_episode_pub_date);
        }
        catch(NotFoundException $e)
        {
            throw new InvalidPodcastException($this->id);
        }
    }

    /**
     * Get the Podcast episodes
     *
     * @return \Ingenious\LaravelListenNotes\Repositories\EpisodeCollection
     */
    public function episodes() : EpisodeCollection
    {
        if ( $this->_episodes->isEmpty() )
            $this->fetchEpisodes();

        return $this->_episodes;
    }

    /**
     * Get the recommendations for the podcast
     *
     * @return mixed
     */
    public function recommendations()
    {
        return app('PodcastSearch')->recommendations($this->id);
    }
}