<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiEnterprisesController;
use App\Http\Controllers\ApiPostsController;
use Illuminate\Support\Facades\Route;


Route::POST("users/register", [ApiAuthController::class, "register"]);
Route::POST("users/login", [ApiAuthController::class, "login"]);
Route::get("test", function () {
    die("Romina test");
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::get("users/me", [ApiAuthController::class, 'me']);
    
    //enterprises
    Route::post("enterprises", [ApiEnterprisesController::class, 'create']);
    Route::get("enterprises", [ApiEnterprisesController::class, 'index']);
    Route::post("enterprises-select", [ApiEnterprisesController::class, 'select']);

    //posts
    Route::post("post-media-upload", [ApiPostsController::class, 'upload_media']);
    Route::post("post", [ApiPostsController::class, 'create_post']);

}); 

Route::get("cases", [ApiPostsController::class, 'index']);
Route::get('process-pending-images', [ApiPostsController::class, 'process_pending_images']);