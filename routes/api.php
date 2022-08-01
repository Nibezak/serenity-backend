<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Super\SuperDashboardController;
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

//Auth Routes here is where you can login,logout,refresh token,user profile and validate OTP Api Routes for your application.

Route::group([
    'middleware' => 'api',
    'prefix' => 'V1'
], function ($router) {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/OTP', [AuthController::class, 'otp']);


    Route::post('/forgot-password', [AuthController::class, 'getPasswordToken']);
    Route::post('/reset-password/{token}', [AuthController::class, 'updatePassword']);
    Route::post('/change-password', [AuthController::class, 'changepassword']);



});


//Routes for Hospital Actvities
Route::group([
    'middleware' => 'api',
    'prefix' => 'staff'
], function ($router) {

    Route::get('/show/roles', [AdminController::class, 'retrieveRoles']);
    Route::post('/new-user', [AdminController::class, 'createNewUser']);
    Route::get('/our-staff', [AdminController::class, 'fetchourstaff']);



});




//Routes for Super Admin Letsreason
Route::group([
    'middleware' => 'api',
    'prefix' => 'Admin'
], function ($router) {

    Route::get('/show/types-of-organization', [SuperDashboardController::class, 'gettypesOrg']);



});
