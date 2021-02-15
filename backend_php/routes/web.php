<?php

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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/', function () use ($router) {
    return "<h1> Botola API </h1>";
});

$router->group(['prefix'=>'api/v1'], function() use($router){
    $router->get('/games', 'GameController@index');
    $router->get('/games/stage/{id}','GameController@gamesByStage');
    $router->get('/games/team/{id}','GameController@showGamesByTeam');
    $router->get('/games/{id}','GameController@oneGame');
    $router->get('/stats', 'GameController@getStats');
    // $router->get('/getGoalsByStage', 'GameController@getGoalsByStage');
    $router->get('/stats/stage/{stage_id}', 'GameController@getStatsByStage');
    $router->get('/stats/team/{team_id}', 'GameController@showStatsByTeam');
    $router->get('/standing', 'GameController@standing');
    $router->get('/teams', 'TeamController@getTeams');
    $router->get('/standing/stage/{stage}','GameController@getStandingByStage');
    $router->get('/standing/positions/{team_id}','GameController@getStandingEveryStage');
    $router->post('/product', 'GameController@create');
    $router->get('/product/{id}', 'GameController@show');
    $router->put('/product/{id}', 'GameController@update');
    $router->delete('/product/{id}', 'GameController@destroy');
    
});
