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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});


$router->post('/takeAuthCode', 'AuthController@takeAuthCode');

$router->post('/generateCode', 'TransactionController@generateCode');
$router->post('/checkCode', 'TransactionController@checkCode');
$router->post('/retrieveInfo', 'TransactionController@retrieveInfo');
$router->post('/getCardsInfo', 'TransactionController@getCardsInfo');
$router->post('/getTransactionData', 'TransactionController@getTransactionData');
$router->post('/getTransactionHistory', 'TransactionController@getTransactionHistory');
$router->post('/checkBalance', 'TransactionController@checkBalance');
$router->post('/requestPayment', 'TransactionController@requestPayment');
$router->post('/makePayment', 'TransactionController@makePayment');
$router->post('/sendMessage', 'TransactionController@sendMessage');

$router->post('/createUser', 'UserController@createUser');
$router->post('/updateUser', 'UserController@updateUser');
$router->post('/getUserProfile', 'UserController@getUserProfile');

