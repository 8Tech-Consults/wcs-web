<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseSuspect;
use App\Models\SuspectLink;
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


    public function link_suspects(Request $r)
    {
        $id_1 = $_GET['id1'];
        $id_2 = $_GET['id2'];
        $suspect_1 = CaseSuspect::find($id_1);
        $suspect_2 = CaseSuspect::find($id_2);

        if ($suspect_1 == null) {
            die("Suspect 1 not found.");
        }
        if ($suspect_2 == null) {
            die("Suspect 2 not found. => $id_2 <= ");
        }

        $instance_1 = SuspectLink::where(['suspect_id_1' => $id_1, 'suspect_id_2' => $id_2])->first();
        if ($instance_1 != null) {
            die("Suspects already linked");
        }
        $instance_2 = SuspectLink::where(['suspect_id_1' => $id_2, 'suspect_id_2' => $id_1])->first();
        if ($instance_2 != null) {
            die("Suspects already linked");
        }

        $link = new SuspectLink();
        $link->suspect_id_1 = $suspect_1->id;
        $link->suspect_id_2 = $suspect_2->id;
        $link->case_id_1 = $suspect_1->case_id;
        $link->case_id_2 = $suspect_2->case_id;
        
        try {
            $link->save();
        } catch (\Exception $e) {
            die("Something went wrong. $e");
        }

        die("SUCCESSFULLY LINKED!");
    }

    public function fingers_to_download(Request $r)
    {
        //$destination = $_SERVER['DOCUMENT_ROOT'].('/uwa/public/storage/images/'); 
        $destination = Utils::docs_root() . '/storage/images/';
        $files = array_diff(scandir($destination), array('.', '..'));
        $my_files = [];
        foreach ($files as $key => $f) {
            $ext = pathinfo($f, PATHINFO_EXTENSION);
            if ($ext != 'bmp') {
                continue;
            }
            $my_files[] = $f;
        }
        die(json_encode($my_files));
    }

    public function fingers_to_download_v2(Request $r)
    {
        $destination = Utils::docs_root() . '/storage/images/';
        $files = array_diff(scandir($destination), array('.', '..'));
        $my_files = [];
        foreach ($files as $key => $f) {
            $ext = pathinfo($f, PATHINFO_EXTENSION);
            if ($ext != 'bmp') {
                continue;
            }
            $my_files[] = $f;
        }

        return $this->success($my_files, 'success');
    }

    public function min_suspects(Request $r)
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

    public function upload_finger(Request $r)
    {

        ini_set('memory_limit', '-1');
        $files = $_FILES;
        $is_single_file = true;
        if ($files == null || empty($files)) {
            return $is_single_file ? "" : [];
        }
        $uploaded_images = array();
        foreach ($files as $file) {

            if (
                isset($file['name']) &&
                isset($file['type']) &&
                isset($file['tmp_name']) &&
                isset($file['error']) &&
                isset($file['size'])
            ) {

                $file_name = $file['name'];
                $destination = Utils::docs_root() . '/storage/images/' . $file_name;

                $res = move_uploaded_file($file['tmp_name'], $destination);
                if (!$res) {
                    continue;
                }
                //$uploaded_images[] = $destination;
                $uploaded_images[] = $file_name;
            }
        }

        $single_file = "";
        if (isset($uploaded_images[0])) {
            $single_file = $uploaded_images[0];
        }
        if (strlen($single_file) > 2) {
            die('success');
        } else {
            die('failed');
        }
    }


    public function min_login(Request $r)
    {
        if ($r->phone_number == null) {
            return $this->error('Email address is required.');
        }
        $phone_number = $r->phone_number;

        if ($r->password == null) {
            return $this->error('Password is required.');
        }

        $u = Administrator::where('phone_number_1', $phone_number)
            ->orWhere('username', $phone_number)
            ->orWhere('email', $phone_number)
            ->first();
        if ($u == null) {
            return $this->error('User account not found.');
        }

        //auth('api')->factory()->setTTL(Carbon::now()->addMonth(12)->timestamp);

        Config::set('jwt.ttl', 60 * 24 * 30 * 365);

        $token = auth('api')->attempt([
            'username' => $phone_number,
            'password' => trim($r->password),
        ]);


        if ($token == null) {
            $token = auth('api')->attempt([
                'phone_number_1' => $phone_number,
                'password' => trim($r->password),
            ]);
        }

        if ($token == null) {
            $token = auth('api')->attempt([
                'email' => $phone_number,
                'password' => trim($r->password),
            ]);
        }

        if ($token == null) {
            return $this->error('Wrong credentials.');
        }
        $u->token = $token;


        return $this->success($u->id, 'Logged in successfully.');
    }
}
