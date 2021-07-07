<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->group([
    'prefix' => 'api'
], function ($router){
    Route::get('country', 'CountryController@listCountry');
    Route::post('country', 'CountryController@addCountry');
    Route::put('country/{id}', 'CountryController@updateCountry');
    Route::delete('country/{id}', 'CountryController@deleteCountry');

    Route::get('state', 'StateController@listState');
    Route::post('country/{id}/state', 'StateController@addState');
    Route::put('state/{id}', 'StateController@updateState');
    Route::delete('state/{id}', 'StateController@deleteState');
});
