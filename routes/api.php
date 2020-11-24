<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', 'API\LoginControlle@login');
Route::post('logout', 'API\LoginControlle@logout');

Route::middleware('auth:api')->group( function () {
    Route::post('fetch_restroom_details', 'API\ServicesController@fetch_restroom_details');
    Route::post('get_restroom', 'API\ServicesController@get_restroom');
    Route::post('fetch_parameter_details', 'API\ServicesController@fetch_parameter_details');
    Route::post('input_data', 'API\ServicesController@inputdata_test');
    Route::post('fetch_input_datas', 'API\ServicesController@fetch_input_datas');
    Route::post('inputdata_update_status', 'API\ServicesController@inputdata_update_status');

    Route::post('get_type', 'API\ServicesController@get_type');

    Route::post('change_password', 'API\ServicesController@change_password');
    Route::post('all_roles', 'API\ServicesController@fetch_all_role_details');
    Route::post('get_users', 'API\ServicesController@fetch_all_users_details');

    Route::post('add_user', 'API\ServicesController@add_user');
    Route::post('edit_user', 'API\ServicesController@edit_user');
    Route::post('delete_user', 'API\ServicesController@delete_user');

    Route::post('fetch_report_datas', 'API\ServicesController@fetch_report_datas');
    Route::post('latest_active', 'API\ServicesController@latest_active');



});
