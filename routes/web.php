<?php

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
Route::get('/register', function () {
  die("register");
})->name("register");
Route::get('/login', function () {
  die("login");
})->name("login");
