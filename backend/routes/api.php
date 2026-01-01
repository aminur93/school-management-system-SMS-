<?php

use App\Http\Controllers\Api\V1\Admin\MediumController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\SchoolClassController;
use App\Http\Controllers\Api\V1\Admin\SectionController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Auth Route start
*/

Route::group(['prefix' => 'v1/auth', 'middleware' => 'throttle:api'], function(){

    /*Register route start*/
    Route::post('/register', [AuthController::class, 'register']);
    /*Register route end*/

    /*Login route start*/
    Route::post('/login', [AuthController::class, 'login']);
    /*Login route end*/

    /*logout and refresh token route start*/
    Route::group(['middleware' => ['api', 'throttle:api']], function() {

        /*logout route start*/
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        /*logout route end*/
    });
    /*logout and refresh token route end*/
});

/**
 * Auth Route end
*/

/**
 * Admin api route start
*/
Route::group(['prefix' => 'v1/admin'], function(){

    /*section route start*/
    Route::get('section', [SectionController::class, 'index']);
    Route::post('section', [SectionController::class, 'store']);
    Route::get('section/{id}', [SectionController::class, 'show']);
    Route::put('section/{id}', [SectionController::class, 'update']);
    Route::delete('section/{id}', [SectionController::class, 'destroy']);
    Route::patch('section/status-update/{id}', [SectionController::class, 'statusUpdate']);
    /*section route end*/

    /*school-class route start*/
    Route::get('school-class', [SchoolClassController::class, 'index']);
    Route::post('school-class', [SchoolClassController::class, 'store']);
    Route::get('school-class/{id}', [SchoolClassController::class, 'show']);
    Route::put('school-class/{id}', [SchoolClassController::class, 'update']);
    Route::delete('school-class/{id}', [SchoolClassController::class, 'destroy']);
    Route::patch('school-class/status-update/{id}', [SchoolClassController::class, 'statusUpdate']);
    /*school-class route end*/

    /*Medium route start*/
    Route::get('medium', [MediumController::class, 'index']);
    Route::post('medium', [MediumController::class, 'store']);
    Route::get('medium/{id}', [MediumController::class, 'show']);
    Route::put('medium/{id}', [MediumController::class, 'update']);
    Route::delete('medium/{id}', [MediumController::class, 'destroy']);
    Route::patch('medium/status-update/{id}', [MediumController::class, 'statusUpdate']);
    /*Medium route end*/
    
    /*user management -> user route start*/
    Route::get('user', [UserController::class, 'index']);
    Route::post('user', [UserController::class, 'store']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    /*user management -> user route end*/

    /*user management -> role route start*/
    Route::get('role', [RoleController::class, 'index']);
    Route::post('role', [RoleController::class, 'store']);
    Route::get('role/{id}', [RoleController::class, 'show']);
    Route::put('role/{id}', [RoleController::class, 'update']);
    Route::delete('role/{id}', [RoleController::class, 'destroy']);
    /*user management -> role route end*/

    /*user management -> permission route start*/
    Route::get('permission', [PermissionController::class, 'index']);
    Route::post('permission', [PermissionController::class, 'store']);
    Route::get('permission/{id}', [PermissionController::class, 'show']);
    Route::put('permission/{id}', [PermissionController::class, 'update']);
    Route::delete('permission/{id}', [PermissionController::class, 'destroy']);
    /*user management -> permission route end*/
});

/**
 * Admin api route end
*/