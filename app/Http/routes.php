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

$app->get('/', 'MasterMindController@appName');

$app->get('/secret-code', 'MasterMindController@showSecretCode');
$app->post('/guess', 'MasterMindController@playUserGuess');
$app->delete('/restart-game', 'MasterMindController@restartGame');

