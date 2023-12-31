<?php

use Illuminate\Http\Request;
use App\Http\Middleware\IsAdmin;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ChaptersController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ChapterStatesController;
use App\Http\Controllers\API\MembershipCategoriesController;


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
    Route::get('cancel-paypal-payment', 'cancelPaypalPayment');

    // Route::get('/clean-payments', "cleanPayments");
});

Route::middleware('auth:sanctum')->controller(RegisterController::class)->group(function(){
    Route::get('profile', 'get');
    Route::patch('profile', 'update');
    Route::get('review-profiles', 'getReviewProfiles')->middleware(IsAdmin::class);
    Route::get('review-profiles/{userId}', 'getSingleReviewProfile')->middleware(IsAdmin::class);
    Route::put('review-profiles/{userId}/update-status', 'updateReviewProfileStatus')->middleware(IsAdmin::class);

    Route::get('/run-migration', function() {
        $output = [];
        \Artisan::call('migrate', $output);
    });

    Route::get('/run-db-seed', function() {
        $output = [];
        \Artisan::call('db:seed', $output);
    });
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

