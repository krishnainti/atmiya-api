<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\MembershipCategoriesController;
use App\Http\Controllers\API\ChaptersController;
use App\Http\Controllers\API\ChapterStatesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('user', 'get');
});


Route::controller(MembershipCategoriesController::class)->group(function(){
    Route::get('/membership-categories', 'get');
});

Route::controller(ChaptersController::class)->group(function(){
    Route::get('/chapters', 'get');
});

Route::controller(ChapterStatesController::class)->group(function(){
    Route::get('/chapter-states', 'get');
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

