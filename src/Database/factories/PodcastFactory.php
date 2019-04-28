<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Carbon\Carbon;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(Podcast::class, function (Faker $faker) {

    $podcast_ids = [
        "b3bb6adfa1354c29a07419c330624dc0",
        "d18417c1459e4a73a6b27ec721a5ca3f",
        "008b1a790d74495a98e9f263cb2416d6",
        "01837b829a7549539513102a68844c72",
        "3a40234223cd4b5ebeaf575b7dc5e6bf",
        "2f465d92f83345f3b9f0a95ef81edc83",
        "e6072eda8e4448858fbbe0c2ecdfb283",
        "b1ae8520c70f45e5a57999120147bce2",
        "a242ff418ff34d35bd720b82d1a54d19",
        "bda17730014841048b374b102c1cf187",
        "c6ec4453f35f4536bcf62fff05696e4c",
        "938de7f853b24bf59c7d199b8fba3950",
        "e14c87ea9d294c3897a4225074e452c0",
        "b0391564a43d4925bad5b7a939502818",
        "8d55f798df684035a941a3016914b316",
        "ccc0715ff181499a84d3b17a80e10a24",
        "a4c9107967b34774b6cc51749c35bdb6",
        "fd2150415c1d40d0b09ba81de48c1541",
        "7a8a900d997a45028bc5fe29e65a45e4",
        "4b20dccd9c5f4b8f9d53d24341b50f93",
    ];

    return [
        "id" => $faker->randomElement($podcast_ids),
        "latest_pub_date" => Carbon::now(),
    ];
});