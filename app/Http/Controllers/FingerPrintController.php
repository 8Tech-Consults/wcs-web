<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseSuspect;
use App\Models\Utils;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class FingerPrintController extends Controller
{

    use ApiResponser;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */





    public function min_login(Request $r)
    {
        $suss = [];
        foreach (CaseSuspect::all() as $key => $s) {
            $sus['id'] = $s->id;
            $sus['name'] = $s->name;
            $sus['uwa_suspect_number'] = $s->uwa_suspect_number;
            $suss[] = $sus;
        }

        die(json_encode($suss));
    }
}
