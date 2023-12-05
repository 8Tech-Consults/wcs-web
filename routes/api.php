<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiEnterprisesController;
use App\Http\Controllers\ApiPostsController;
use App\Http\Controllers\FingerPrintController;
use App\Models\TempData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    $data['name'] = 'Muhindo Mubaraka';
    $data['sex'] = 'Male';
    $data['country'] = 'Uganda';

    die(json_encode($data));
});

Route::get('test-2', function () {
    $data['name'] = 'Muhindo Mubaraka';
    $data['sex'] = 'Male';
    $data['country'] = 'Uganda';

    $list[] = $data;

    $data['name'] = 'Masiak Amina';
    $data['sex'] = 'Female';
    $data['country'] = 'Kenya';

    $list[] = $data;
    die(json_encode($list));
});

Route::POST("users/register", [ApiAuthController::class, "register"]);
Route::POST("users/login", [ApiAuthController::class, "login"]);
Route::POST("users/min/login", [FingerPrintController::class, "min_login"]);
Route::GET("min/suspects", [FingerPrintController::class, "min_suspects"]);
Route::GET("min/fingers-to-download", [FingerPrintController::class, "fingers_to_download"]);
Route::GET("min/fingers-to-download-v2", [FingerPrintController::class, "fingers_to_download_v2"]);
Route::GET("min/link-suspects", [FingerPrintController::class, "link_suspects"]);
Route::POST("min/upload-finger", [FingerPrintController::class, "upload_finger"]);
Route::POST("temp-data", function (Request $r) {
    if (
        $r->user_id == null ||
        $r->type == null
    ) {
        return 0;
    }

    $d = new TempData();
    $d->administrator_id = ((int)($r->user_id));
    $d->type = $r->type;
    $d->data = json_encode($_POST);
    $d->save();
    return 1;
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::get("users/me", [ApiAuthController::class, 'me']);

    //enterprises
    Route::post("enterprises", [ApiEnterprisesController::class, 'create']);
    Route::get("enterprises", [ApiEnterprisesController::class, 'index']);
    Route::post("enterprises-select", [ApiEnterprisesController::class, 'select']);

    //posts
    Route::post("post-media-upload", [ApiPostsController::class, 'upload_media']);
    Route::post("cases", [ApiPostsController::class, 'create_post']);
    Route::post("update-profile", [ApiPostsController::class, 'update_profile']);
    Route::post("password-change", [ApiPostsController::class, 'password_change']);
    Route::get("offences", [ApiPostsController::class, 'offences']);
});

Route::get("cases", [ApiPostsController::class, 'index']);
Route::get("detection-methods", [ApiPostsController::class, 'detection_methods']);
Route::get("courts", [ApiPostsController::class, 'courts']);
Route::get("protected-areas", [ApiPostsController::class, 'protected_areas']);
Route::get("animals", [ApiPostsController::class, 'animals']);
Route::get("specimens", [ApiPostsController::class, 'specimens']);
Route::get("implements", [ApiPostsController::class, 'implements']);
Route::get("conservation-areas", [ApiPostsController::class, 'conservation_areas']);
Route::get('process-pending-images', [ApiPostsController::class, 'process_pending_images']);


Route::get('ajax', function (Request $r) {

    $_model = trim($r->get('model'));
    $conditions = [];
    foreach ($_GET as $key => $v) {
        if (substr($key, 0, 6) != 'query_') {
            continue;
        }
        $_key = str_replace('query_', "", $key);
        $conditions[$_key] = $v;
    }

    if (strlen($_model) < 2) {
        return [
            'data' => []
        ];
    }

    $model = "App\Models\\" . $_model;
    $search_by_1 = trim($r->get('search_by_1'));
    $search_by_2 = trim($r->get('search_by_2'));

    $q = trim($r->get('q'));

    $res_1 = $model::where(
        $search_by_1,
        'like',
        "%$q%"
    )
        ->where($conditions)
        ->limit(20)->get();
    $res_2 = [];

    if ((count($res_1) < 20) && (strlen($search_by_2) > 1)) {
        $res_2 = $model::where(
            $search_by_2,
            'like',
            "%$q%"
        )
            ->where($conditions)
            ->limit(20)->get();
    }

    $data = [];
    foreach ($res_1 as $key => $v) {
        $name = "";
        if (isset($v->$search_by_1)) {
            $name = $v->$search_by_1;
        }
        $data[] = [
            'id' => $v->id,
            'text' =>  $name
        ];
    }
    foreach ($res_2 as $key => $v) {
        $name = "";
        if (isset($v->name)) {
            $name = $v->$search_by_1;
        }
        $data[] = [
            'id' => $v->id,
            'text' =>  $name
        ];
    }

    return [
        'data' => $data
    ];
});
