<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */


use Illuminate\Support\Str;
use Faker\Generator as Faker;

use Ingenious\LaravelListenNotes\Models\Episode;
use Ingenious\LaravelListenNotes\Models\Podcast;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Episode::class, function (Faker $faker) {

    return [
        //"podcast_title_highlighted" => $faker->sentence,
        "id" => str_random(48),
        "podcast_id" => factory(Podcast::class)
    ];
});
