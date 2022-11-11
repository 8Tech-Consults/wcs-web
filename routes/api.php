<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiEnterprisesController;
use App\Http\Controllers\ApiPostsController;
use Illuminate\Http\Request;
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
    Route::post("cases", [ApiPostsController::class, 'create_post']);
});

Route::get("cases", [ApiPostsController::class, 'index']);
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
            $name = " - " . $v->$search_by_1;
        }
        $data[] = [
            'id' => $v->id,
            'text' => "#$v->id" . $name
        ];
    }
    foreach ($res_2 as $key => $v) {
        $name = "";
        if (isset($v->name)) {
            $name = " - " . $v->$search_by_1;
        }
        $data[] = [
            'id' => $v->id,
            'text' => "#$v->id" . $name
        ];
    }

    return [
        'data' => $data
    ];
});
