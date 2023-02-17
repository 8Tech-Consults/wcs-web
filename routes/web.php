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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

Route::match(['get', 'post'], '/print', [PrintController2::class, 'index']);
Route::get('/password-forget-email', [MainController::class, 'password_forget_email'])->name("password-forget-email");
Route::post('/password-forget-email', [MainController::class, 'password_forget_email_post']);
Route::get('/password-forget-code', [MainController::class, 'password_forget_code'])->name("password-forget-code");
Route::get('/2fauth', [MainController::class, 'two_f_auth'])->name("two_f_auth");
Route::POST("do-change-password", [MainController::class, "doChangePassword"]);
Route::get('/logout', function () {
  return redirect(admin_url('auth/logout?log_me_out=1'));
  header('Location: ' . admin_url('auth/logout')); 
  Auth::logout();
  return redirect('/login'); 
});
Route::get('/login', function () {
  die("login");
})->name("login");

Route::get('/register', function () {
  die("register");
})->name("register");
Route::get('/login', function () {
  die("login");
})->name("login");

Route::post('/2f-auth-code', function (Request $t) {
  $acc = Administrator::where(['code' => trim($t->code)])->first();
  if ($acc == null) {
    return Redirect::back()->withErrors(['code' => ['Enter correct code.']])->withInput();
  }
  $acc->authenticated = 1;
  $acc->save();
  return redirect(admin_url());
});
