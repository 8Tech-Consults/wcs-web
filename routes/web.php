<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\PrintController2;
use App\Models\AcademicClass;
use App\Models\Book;
use App\Models\BooksCategory;
use App\Models\Course;
use App\Models\StudentHasClass;
use App\Models\Subject;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Route;
use Mockery\Matcher\Subset;
use Faker\Factory as Faker;


Route::match(['get', 'post'], '/print', [PrintController2::class, 'index']);
Route::get('/password-forget-email', [MainController::class, 'password_forget_email'])->name("password-forget-email");
Route::post('/password-forget-email', [MainController::class, 'password_forget_email_post']);
Route::get('/password-forget-code', [MainController::class, 'password_forget_code'])->name("password-forget-code"); 
Route::POST("do-change-password", [MainController::class, "doChangePassword"]); 
Route::get('/register', function () {
  die("register");
})->name("register");
Route::get('/login', function () {
  die("login");
})->name("login");
