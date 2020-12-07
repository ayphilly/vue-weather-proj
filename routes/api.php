<?php

use Zttp\Zttp;
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

Route::get('/weather', function () {
    $apiKey = config('services.weather.key');
    $area = request('area');
    // $response = Zttp::get("http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=London&days=5");
    $response = Zttp::get("http://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=London&days=5");
    return $response->json();
});