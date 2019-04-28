<?php

namespace Ingenious\LaravelListenNotes\Exceptions;

class InvalidPodcastException extends \Exception
{
    public function __construct($podcast_id)
    {
        $this->podcast_id = $podcast_id;
    }

    public function response()
    {
        return response()->json([
            'errors' => true,
            'message' => "Oops. Podcast with id {$this->podcast_id} does not appear to be a valid podcast."
        ], 422);
    }
}