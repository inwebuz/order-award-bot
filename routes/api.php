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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('attributes', 'Api\AttributesController@index');
Route::get('attributes/{attribute}', 'Api\AttributesController@show');
Route::get('attributes/{attribute}/attribute-values', 'Api\AttributesController@attributeValues');

Route::get('attribute-values', 'Api\AttributeValuesController@index');
Route::get('attribute-values/{attributeValue}', 'Api\AttributeValuesController@show');
