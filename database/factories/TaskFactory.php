<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Category;
use App\Models\PriorityLevel;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'creator_id'        => factory(User::class)->create()->id,
        'assignee_id'       => factory(User::class)->create()->id,
        'priority_level_id' => factory(PriorityLevel::class)->create()->id,
	    'category_id'       => factory(Category::class)->create()->id,
	    'title'             => $faker->sentence,
	    'content'           => $faker->paragraph
    ];
});
