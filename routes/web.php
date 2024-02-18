<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\PrintController2;
use App\Models\AcademicClass;
use App\Models\Book;
use App\Models\BooksCategory;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\Course;
use App\Models\Gen;
use App\Models\StudentHasClass;
use App\Models\Subject;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Route;
use Mockery\Matcher\Subset;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

Route::get('transfer-data', function () {
  $_suspects = DB::connection('db2')->table('suspects')->get();
  ini_set('max_execution_time', '-1');
  foreach ($_suspects as $key => $_old) {
    $id_old = $_old->id;
    $case = CaseModel::where(['id_old' => $id_old])->first();
    if ($case == null) {
      $case = new CaseModel();
      die("Case not found");
    } else {
      //continue;
    }

    $sus = CaseSuspect::where(['id_old' => $id_old])->first();
    if ($sus == null) {
      $sus = new CaseSuspect();
    } else {
      continue;
    }
    $sus->id_old = $id_old;
    $sus->created_at = Carbon::parse($_old->REGISTERED);
    $sus->updated_at = Carbon::parse($_old->CHANGED);
    $sus->case_id = $case->id;
    $names = explode(" ", $_old->SUSPECT_NAME);
    if (isset($names[0])) {
      $sus->first_name = trim($names[0]);
    }
    if (isset($names[1])) {
      $sus->last_name = trim($names[1]);
    }
    if (isset($names[2])) {
      $sus->middle_name = trim($names[2]);
    }
    $sus->national_id_number = $_old->SUSPECT_ID;
    if (strlen($sus->national_id_number) < 1) {
      $sus->national_id_number = $_old->SUSPECT_PASSPORT_NO;
      if (strlen($sus->national_id_number) < 1) {
        $sus->national_id_number = $_old->SUSPECT_DR_LICENCE;
        if (strlen($sus->national_id_number) < 1) {
          $sus->national_id_number = $_old->SUSPECT_VOTERS_CARD;
        }
      }
    }
    $sus->sex = 'Male';
    if (
      $_old->GENDER == 'f' ||
      $_old->GENDER == 'Female' ||
      $_old->GENDER == 'female' ||
      $_old->GENDER == 'female'
    ) {
      $sus->sex = 'Female';
    }
    $sus->age = $_old->YOB;
    $sus->occuptaion = $_old->OCCUPATION;
    $sus->country = $_old->SUSPECT_NATIONALITY;
    $sus->sub_county_id = $_old->SUSPECT_NATIONALITY;
    $sus->parish = 'N/A';
    $sus->village = 'N/A';
    $sus->parish = $_old->PARISH;
    $sus->parish = $_old->ETHNICITY;
    $sus->ethnicity = $_old->ETHNICITY;
    $sus->is_suspects_arrested = 'No';
    $sus->save();

    echo ($sus->id . ". " . $sus->first_name . " " . $sus->last_name . "<br>");


    continue;
    /*   
 

arrest_date_time
arrest_district_id
arrest_sub_county_id
arrest_parish
arrest_village
arrest_latitude
arrest_longitude
arrest_first_police_station
arrest_current_police_station
arrest_agency
other_arrest_agencies
arrest_uwa_unit
arrest_detection_method
arrest_uwa_number
arrest_crb_number
is_suspect_appear_in_court
prosecutor
is_convicted
case_outcome
magistrate_name
court_name
court_file_number
is_jailed
jail_period
is_fined
fined_amount
status
deleted_at
photo
court_date
jail_date
use_same_arrest_information
use_same_court_information
suspect_number
arrest_in_pa
pa_id
management_action
community_service
reported_by
add_more_suspects
ca_id
not_arrested_remarks
police_sd_number
police_action
police_action_date
police_action_remarks
court_file_status
court_status
suspect_court_outcome
is_ugandan
use_offence
use_offence_suspect_id
use_same_arrest_information_id
use_same_court_information_id
use_same_arrest_information_coped
prison
jail_release_date
suspect_appealed
suspect_appealed_date
suspect_appealed_court_name
suspect_appealed_court_file
use_same_court_information_coped
use_offence_suspect_coped
type_of_id
community_service_duration
suspect_appealed_outcome
suspect_appeal_remarks
cautioned
cautioned_remarks
other_court_name
unique_id
id_old



    */
    $case->id_old = $id_old;
    $case->created_at = Carbon::parse($_old->REGISTERED);
    $case->case_date = Carbon::parse($_old->REGISTERED);
    $case->created_at = Carbon::parse($_old->CHANGED);
    $case->updated_at = $case->created_at;
    $case->reported_by = 1;
    $case->latitude = $_old->HOME_LAT;
    $case->longitude = $_old->HOME_LON;
    $case->district_id = '2';
    $case->sub_county_id = '1001472';
    $case->parish = 'N/A';
    $case->village = 'N/A';
    $case->is_offence_committed_in_pa = 'No';
    $case->add_more_suspects = 'No';
    $case->has_exhibits = 'No';
    $case->done_adding_suspects = 'Yes';
    $case->case_submitted = 'Yes';
    $case->detection_method = 'N\A';
    $case->pa_id = 1;
    $case->case_step = 4;
    $case->title = 'Uganda Vs ' . $_old->SUSPECT_NAME;
    $case->court_file_status = 'Closed';
    $case->officer_in_charge =   $_old->CHANGEDBY;
    $case->officer_in_charge =   $_old->NOTES;
    $case->save();
    echo ($case->id . " " . $case->title . "<br>");
  }
  die();



  // dd($_suspects);
  $_cases = DB::connection('db2')->table('cases')->get();
  foreach ($_cases as $key => $_case) {
    $id_old = $_case->id;
    $case = CaseModel::where(['id_old' => $id_old])->first();
    if ($case == null) {
      $case = new CaseModel();
    } else {
      //continue;
    }
    $case->id_old = $id_old;
    $case->created_at = Carbon::parse($_case->REGISTERED);
    $case->updated_at = $case->created_at;
    $case->case_number = $_case->UWA_CASEID;
    $case->title = $_case->CRIME_TYPE;
    echo ($case->id . " " . $case->title . "<br>");
    $case->save();
    // echo ($case->title . "<br>");
  }

  //dd($_cases);
})->name("transfer-data");
/* 
+"UWA_CASEID": "HQ-1-2016-04-20-1" ==> case_number
+"POLICE_CASEID": "HQ-1-2016-04-20-1" ==> case_number
title ==> CRIME_TYPE;
POLICE_CASE_ID*
COURTDATE*
ENDEDDATE* 
UWA_PROSECUTOR* 
STATE_PROSECUTOR* 
DEFENCE_LAWYER* 
MAGISTRATE* 

+"DEFENDANTSTABLE": 1466695954
*/
Route::get('/login', function () {
  die("login");
})->name("login");

Route::get('/gen', function () {
  die(Gen::find($_GET['id'])->do_get());
})->name("gen");
Route::get('/mobile', function () {
  return redirect(url('uwa-v15.0.apk'));
})->name("mobile");
Route::get('/desktop', function () {
  return redirect(url('uwa-desktop-v15.zip'));
})->name("desktop");
Route::get('/fingerprint', function () {
  return redirect(url('uwa-fingerprint-v2.zip'));
})->name("fingerprint");
Route::get('/drivers', function () {
  return redirect(url('uwa-fingeprint-drivers.zip'));
})->name("drivers");
Route::get('/user-manual-mobile', function () {
  return redirect(url('OWODAT-Mobile-App-User-Manual.pdf'));
});
Route::get('/user-manual-web', function () {
  return redirect(url('OWODAT-Web-Systeem-User-Manual.pdf'));
});

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


Route::get('optimize', function () {
  \Artisan::call('cache:clear');
  \Artisan::call('view:clear');
  \Artisan::call('config:clear');
  \Artisan::call('config:cache');
  return "done";
});
