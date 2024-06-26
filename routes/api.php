<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\JWTController;
use App\Http\UserController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'users'], function ($router) {
    Route::post('/register', 'JWTController@register');
    Route::post('/logout', 'JWTController@logout');
    // Route::post('/refresh', 'JWTController@refresh');
    Route::post('/perfile', 'JWTController@perfile');

    Route::post('/login', 'JWTController@loginAdmin');
    Route::post('/login_ecommerce', 'JWTController@loginEcommerce');
    
    Route::group(['prefix' => 'admin'], function ($router) {
        Route::get('/all', 'UserController@index');
        Route::post('/register', 'UserController@store');
        Route::put('/update/{id}', 'UserController@update');
        Route::delete('/delete/{id}', 'UserController@destroy');
    });
});