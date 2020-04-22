<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::namespace('Api')->group(function(){
    Route::middleware('auth:api')->group(function() {
        Route::prefix('priority-levels')->group(function () {
            Route::get('/', 'PriorityLevelController@index');
        });

        Route::prefix('comments')->group(function () {
            Route::post('/', 'CommentController@store');
        });

        Route::prefix('tasks')->group(function () {
            Route::get('/', 'TaskController@index');
            Route::get('/{task}', 'TaskController@show');
            Route::post('/', 'TaskController@store');
            Route::put('/{task}', 'TaskController@update');
            Route::delete('/{task}', 'TaskController@destroy');
            Route::delete('/{task}/users/{user}', 'TaskController@detachFollower');
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', 'CategoryController@index');
            Route::get('/{category}', 'CategoryController@show');
            Route::post('/', 'CategoryController@store');
            Route::put('/{category}', 'CategoryController@update');
            Route::delete('/{category}', 'CategoryController@destroy');
        });
        Route::prefix('users')->group(function () {
            Route::get('/', 'UserController@index');
            Route::get('/me', 'UserController@me');
            Route::put('/{user}', 'UserController@update');
            Route::post('/', 'UserController@store');
            Route::delete('/{user}', 'UserController@destroy');
        });

        Route::prefix('messages')->group(function () {
            Route::get('/{id}', 'MessageController@index');
            Route::put('/read', 'MessageController@updateMessagesToRead');
            Route::post('/', 'MessageController@store');
            Route::post('/event', 'MessageController@chatEvent');
        });

        Route::prefix('channel-messages')->group(function () {
            Route::get('/{id}', 'ChannelMessageController@index');
            Route::put('/read', 'ChannelMessageController@updateChannelMessagesToRead');
            Route::post('/', 'ChannelMessageController@store');
            Route::post('/event', 'ChannelMessageController@chatEvent');
        });

        Route::prefix('channels')->group(function () {
            Route::get('/my-channels', 'ChannelController@myChannel');
            Route::get('/check', 'ChannelController@checkChannelExists');
            Route::get('/{id}', 'ChannelController@getChannelInformation');
            Route::get('/auth/{channelId}', 'ChannelController@checkAuth');
            Route::put('/{channel}', 'ChannelController@update');
            Route::post('/', 'ChannelController@store');
            Route::post('/add-people', 'ChannelController@addPeople');
            Route::post('/created', 'ChannelController@sendChannelCreatedEvent');
            Route::delete('/{channel}', 'ChannelController@destroy');
        });

        Route::prefix('permissions')->group(function () {
            Route::get('/me', 'PermissionController@getMyPermission');
            Route::get('/check', 'PermissionController@checkPermission');
            Route::get('/{user}', 'PermissionController@index');
            Route::put('/{user}', 'PermissionController@update');
        });
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', 'AuthController@login');
        Route::post('/register', 'AuthController@register');

        Route::middleware('auth:api')->group(function () {
            Route::get('/logout', 'AuthController@logout');
        });
    });
});
