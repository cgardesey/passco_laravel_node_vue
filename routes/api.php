<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/save_question','ApiController@saveQuestion');
Route::post('/update_question/{questionid}','ApiController@updateQuestion');
Route::get('/questions','ApiController@getQuestions');
Route::get('/question/{questionid}','ApiController@getQuestion');
Route::get('/year_questions/{search}', 'ApiController@getYearQuestions');
Route::post('/save_subscriber','ApiController@saveSubscriber');
Route::post('/update_subscriber/{subscriberid}','ApiController@updateSubscriber');
Route::get('/subscribers','ApiController@getSubscribers');
Route::get('/subscriber/{number}','ApiController@getSubscriber');


Route::resource('transactions', 'TransactionsController');

Route::resource('users', 'UsersController');