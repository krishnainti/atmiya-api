<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\MembershipCategoriesController;
use App\Http\Controllers\API\ChaptersController;
use App\Http\Controllers\API\ChapterStatesController;
use App\Http\Middleware\IsAdmin;


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
    Route::post('submit-profile', 'submitProfile');
    Route::get('find-profile-by-email', 'findProfileByEmail');
    Route::get('capture-paypal-payment', 'captureRegistrationPaypalPaymentOrder');
});

Route::middleware('auth:sanctum')->controller(RegisterController::class)->group(function(){
    Route::get('profile', 'get');
    Route::patch('profile', 'update');
    Route::get('review-profiles', 'getReviewProfiles')->middleware(IsAdmin::class);
    Route::get('review-profiles/{userId}', 'getSingleReviewProfile')->middleware(IsAdmin::class);
    Route::put('review-profiles/{userId}/update-status', 'updateReviewProfileStatus')->middleware(IsAdmin::class);
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

