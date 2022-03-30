<?php

/** @var Router $router */

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

use Laravel\Lumen\Routing\Router;

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
    $router->get('{id}', 'InventoryController@get');
    $router->post('', 'InventoryController@getAll');
    $router->post('create', 'InventoryController@create');
    $router->delete('delete', 'InventoryController@delete');
    $router->put('edit', 'InventoryController@edit');

    $router->group(['prefix' => 'product'], function () use ($router) {
        $router->get('{id}', 'ProductController@get');
        $router->post('', 'ProductController@getAll');
        $router->post('add', 'ProductController@add');
        $router->put('edit', 'ProductController@edit');
        $router->delete('delete', 'ProductController@delete');
    });
});
