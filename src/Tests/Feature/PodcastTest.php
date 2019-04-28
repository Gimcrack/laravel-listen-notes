<?php

namespace Ingenious\LaravelListenNotes\Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Ingenious\LaravelListenNotes\Repositories\Episode;
use Ingenious\LaravelListenNotes\Repositories\EpisodeCollection;
use Ingenious\LaravelListenNotes\Repositories\Genre;
use Ingenious\LaravelListenNotes\Repositories\GenreCollection;
use Ingenious\LaravelListenNotes\Repositories\Podcast;
use Ingenious\LaravelListenNotes\Repositories\PodcastCollection;
use Ingenious\LaravelListenNotes\Tests\TestCase;


class PodcastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The id for Planet Money
     *
     * @var string
     */
    protected $podcast_id = "8cb941141a7c434d945397cfd8b12e58";

    /**
     * A Planet Money Episode
     *
     * @var string
     */
    protected $episode_id = "b773f60b508245939b6c728bcf9ecc38";

    /**
     * History
     *
     * @var int
     */
    protected $genre_id = 125;

    /** @test */
    public function it_can_search_for_podcasts()
    {
        $response = $this->post("api/v1/search",[
            'term' => "This American Life"
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(["title_original" => "This American Life"]);
    }

    /** @test */
    public function it_can_get_a_podcast_by_id()
    {
        $response = $this->get("api/v1/podcasts/{$this->podcast_id}");

        $response->assertStatus(200)
            ->assertJsonFragment(["title" => "Planet Money"]);
    }

    /** @test */
    public function it_can_get_a_podcasts_episodes()
    {
        $response = $this->get("api/v1/podcasts/{$this->podcast_id}/episodes");

        $response->assertStatus(200);

        $this->assertTrue( $response->json()[0]['audio_length'] > 0 );
    }

    /** @test */
    public function it_can_get_an_episode_by_id()
    {
        $response = $this->get("api/v1/episodes/{$this->episode_id}");

        $response->assertStatus(200)
            ->assertJsonFragment(["title" => "#902: The Phoebus Cartel"]);
    }

    /** @test */
    public function it_can_get_the_list_of_geners()
    {
        $response = $this->get("api/v1/genres");

        $response->assertStatus(200);

        $this->assertTrue( count($response->json()) > 100 );
    }

    /** @test */
    public function it_can_get_a_genre()
    {
        // set up, pre-populate genres
        app('PodcastSearch')->genres();

        $response = $this->get("api/v1/genres/{$this->genre_id}");

        $response->assertStatus(200)
            ->assertJsonFragment(["name" => "History"]);
    }

    /** @test */
    public function it_can_browse_a_genre()
    {
        // set up, pre-populate genres
        app('PodcastSearch')->genres();

        $response = $this->get("api/v1/genres/{$this->genre_id}/browse/1");

        $response->assertStatus(200);

        $this->assertNotNull( $title1 = $response->json()[0]['title'] );

        $response2 = $this->get("api/v1/genres/{$this->genre_id}/browse/2");

        $response2->assertStatus(200);

        $this->assertNotNull( $title2 = $response2->json()[0]['title'] );

        $this->assertNotEquals($title1, $title2);
    }
}
