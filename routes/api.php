<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('json','App\Http\Controllers\JsonController');
Route::resource('jsonScore','App\Http\Controllers\JsonScoreController');
Route::get('jsonScore/show/groupArray','App\Http\Controllers\JsonScoreController@groupArray');
Route::post('jsonScore/create/allLeagueScores','App\Http\Controllers\JsonScoreController@ScoreAllLeagues');
Route::get('jsonScore/show/allLeagueScores','App\Http\Controllers\JsonScoreController@getScoreAllLeagues');
Route::get('json/show/tabNames','App\Http\Controllers\JsonController@tabNames');
Route::get('json/show/league','App\Http\Controllers\JsonController@league');
Route::get('json/show/leagueTable','App\Http\Controllers\JsonController@leagueTable');
