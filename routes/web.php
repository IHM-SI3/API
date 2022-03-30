<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('{id}', 'UserController@get');
    $router->post('', 'UserController@getAll');
    $router->post('register', 'UserController@register');
    $router->post('login', 'UserController@login');
    $router->post('logout', 'UserController@logout');
    $router->delete('delete', 'UserController@delete');
    $router->put('edit', 'UserController@edit');
});

$router->group(['prefix' => 'inventory'], function () use ($router) {
    $router->get('{id}', function () {
        echo "oui";
    });
    $router->post('', function () {
        echo "oui";
    });
    $router->post('create', function () {
        echo "oui";
    });
    $router->post('delete', function () {
        echo "oui";
    });
    $router->post('edit', function () {
        echo "oui";
    });
    $router->group(['prefix' => 'product'], function () use ($router) {
        $router->get('{id}', function () {
            echo "oui";
        });
        $router->post('', function () {
            echo "oui";
        });
        $router->post('add', function () {
            echo "oui";
        });
        $router->put('edit', function () {
            echo "oui";
        });
        $router->delete('delete', function () {
            echo "oui";
        });
    });
});
