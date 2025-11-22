<?php

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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




// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return Auth::user();
// });

// Route::get('/teacher', function(){
//     return Teacher::all();
// });

// Route::post('/teachers', 'TeacherController@checkteacherauthapi');

Route::group(['namespace'=>'Api'], function(){
    // Route::post('teachers',["App\Http\Controllers\Api\TeacherController","checkteacherauthapi"]);
    // Route::post('/teachers', 'TeacherController@checkteacherauthapi');

});

