<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$baseUrl = '/bots/quoteBot/';
$token = env('BOT_TOKEN');

$app->get($baseUrl, 'QuoteController@index');
$app->get($baseUrl.'delete/{id}', 'QuoteController@delete');
$app->get($baseUrl.$token, function() use ($app) {
    return $app->welcome();
});

$app->post($baseUrl.$token, 'QuoteController@processRequest');
