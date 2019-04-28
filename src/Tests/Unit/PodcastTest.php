<?php

namespace Ingenious\LaravelListenNotes\Tests\Unit;


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

    /** @test */
    public function it_can_search_for_podcasts()
    {
        $p = app('PodcastSearch')
            ->searchPodcasts('This American Life')
            ->first();

        $this->assertInstanceOf(Podcast::class, $p);
        $this->assertEquals("This American Life", $p->title);
    }

    /** @test */
    public function it_can_get_a_podcast_by_name()
    {
        $p = app('PodcastSearch')
            ->podcastByName('This American Life');

        $this->assertInstanceOf(Podcast::class, $p);
        $this->assertEquals("This American Life", $p->title);
    }

    /** @test */
    public function it_can_get_a_podcasts_episodes()
    {
        $p = app('PodcastSearch')
            ->podcastByName('This American Life');

        $this->assertTrue($p->episodes()->count() > 1);
        $this->assertInstanceOf(EpisodeCollection::class, $p->episodes());
        $this->assertInstanceOf(Episode::class, $p->episodes()->first());
    }

    /** @test */
    public function it_can_get_an_episode_by_id()
    {
        $p = app('PodcastSearch')
            ->podcastByName('This American Life');

        $episode_id = $p->episodes()->first()->id;

        $e = app('PodcastSearch')->episode($episode_id);

        $this->assertInstanceOf(Episode::class, $e);
        $this->assertEquals($e->id, $episode_id);
    }

    /** @test */
    public function it_can_get_a_podcasts_recommendations()
    {
        $p = app('PodcastSearch')
            ->podcastByName('This American Life');

        $this->assertInstanceOf(PodcastCollection::class, $p->recommendations());
    }

    /** @test */
    public function it_can_get_a_collection_of_genres()
    {
        $g = app('PodcastSearch')
            ->genres();

        $this->assertInstanceOf(GenreCollection::class, $g);

        $this->assertInstanceOf(Genre::class, $g->first());

        $this->assertNotNull($g->first()->name);
    }

    /** @test */
    public function it_can_search_the_podcasts_of_a_genre()
    {
        $g = app('PodcastSearch')
            ->genres()
            ->first();

        $p = $g->podcasts( $q = "podcast" );

        $this->assertInstanceOf(PodcastCollection::class, $p);

        $this->assertNotNull($p->first()->title);
    }

    /** @test */
    public function it_can_search_the_episodes_of_a_genre()
    {
        $g = app('PodcastSearch')
            ->genres()
            ->first();

        $p = $g->episodes( $q = "podcast" );

        $this->assertInstanceOf(EpisodeCollection::class, $p);

        $this->assertNotNull($p->first()->title);
    }

    /** @test */
    public function it_can_get_the_best_podcasts_of_a_genre()
    {
        $g = app('PodcastSearch')
            ->genres()
            ->first();

        $p = $g->bestPodcasts();

        $this->assertInstanceOf(PodcastCollection::class, $p);

        $this->assertNotNull($p->first()->title);
    }

    /**
     * @test
     * @expectedException \Ingenious\LaravelListenNotes\Exceptions\InvalidPodcastException
     */
    public function it_throws_an_exception_when_fetching_an_invalid_podcast()
    {
        \Podcast::find(1);
    }
}
