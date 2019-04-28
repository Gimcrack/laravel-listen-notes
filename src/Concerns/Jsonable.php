<?php

namespace Ingenious\LaravelListenNotes\Concerns;

trait Jsonable
{
    protected $_meta;

    /**
     * Get the metadata for the Podcast
     *
     * @return object
     */
    public function meta()
    {
        return $this->_meta;
    }

    /**
     * Get the json representation of the Podcast
     *
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->meta());
    }

    /**
     * Get the array representation of the Podcast
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this->meta();
    }
}