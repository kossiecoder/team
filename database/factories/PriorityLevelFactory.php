<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\PriorityLevel;
use Faker\Generator as Faker;

$factory->define(PriorityLevel::class, function (Faker $faker) {
    return [
        'name'  => 'Normal',
        'theme' => 'success',
        'code'  => 'normal'
    ];
});
