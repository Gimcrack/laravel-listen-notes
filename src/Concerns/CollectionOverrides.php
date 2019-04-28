<?php

namespace Ingenious\LaravelListenNotes\Concerns;

trait CollectionOverrides
{
    public function toJson($options = [])
    {
        return $this->map->toJson();
    }

    public function toArray()
    {
        return $this->map->toArray()->all();
    }
}