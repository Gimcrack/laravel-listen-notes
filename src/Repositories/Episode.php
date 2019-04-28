<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use Ingenious\LaravelListenNotes\Concerns\Jsonable;
use Ingenious\LaravelListenNotes\Models\Episode as EpisodeModel;
use function property_exists;

/**
 * @property  string id
 */
class Episode
{
    use Jsonable;

    protected $model;

    public static function find($id)
    {
        if ( $model = EpisodeModel::find($id) )
            return new static($model);

        return new static(compact('id'));
    }

    public function __construct($meta, $podcast = null)
    {
        if ( is_object($meta) && get_class($meta) === EpisodeModel::class) {
            $this->model = $meta;
            $this->_meta = (object) [];

            $this->hydrate();
        }

        else {
            $this->_meta = (object) $meta;
            $this->model = new EpisodeModel;

            if ( property_exists($this->_meta, "podcast") )
                $this->_meta->podcast = (object) $this->_meta->podcast;

            elseif ( $podcast )
                $this->_meta->podcast = (object) $podcast;

            else
                $this->hydrate();

            $this->persist();
        }
    }

    public function __get($key)
    {
        if( property_exists($this->_meta, $key))
            return $this->_meta->$key;

        if( property_exists($this->_meta, $alternate = $key . "_original"))
            return $this->_meta->$alternate;

        return $this->model->$key;
    }

    public function persist()
    {
        $this->model = EpisodeModel::firstOrCreate([
            'id' => $this->id,
            'podcast_id' => $this->podcast->id,
        ], [
            'pub_date_ms' => $this->pub_date_ms
        ]);

        return $this->model;
    }

    public function model()
    {
        return $this->model ?? $this->persist();
    }

    public function hydrate()
    {
        $response = app('PodcastSearch')->episodeMeta($this->id)->json();

        $this->_meta = $response;

        $this->_meta->podcast = (object) $response->podcast;
    }
}