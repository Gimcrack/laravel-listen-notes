<?php

namespace Ingenious\LaravelListenNotes\Repositories;

use function get_class;
use Ingenious\LaravelListenNotes\Concerns\Jsonable;
use Ingenious\LaravelListenNotes\Models\Genre as GenreModel;
use function is_object;
use function property_exists;

/**
 * @property  int id
 * @property string|null name
 * @property int|null parent_id
 */
class Genre
{
    use Jsonable;

    protected $model;

    public static function find($id)
    {
        return new static(GenreModel::find($id));
    }

    public function __construct($meta)
    {
        if ( is_object($meta) && get_class($meta) === GenreModel::class) {
            $this->model = $meta;
            $this->_meta = (object) $meta->toArray();
        }

        else {
            $this->_meta = (object) $meta;
            $this->model = new GenreModel;

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
        $this->model = GenreModel::firstOrCreate([
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id
        ])->with('parent.parent.parent');

        return $this->model;
    }

    public function model()
    {
        return $this->model ?? $this->persist();
    }

    public function podcasts($q = "")
    {
        return app('PodcastSearch')
            ->searchPodcasts($q, [
                "genre_ids" => $this->id
            ]);
    }

    public function episodes($q = "")
    {
        return app('PodcastSearch')
            ->searchEpisodes($q, [
                "genre_ids" => $this->id
            ]);
    }

    public function bestPodcasts($page = 1)
    {
        return app('PodcastSearch')
            ->bestPodcasts($this->id, $page);
    }


}