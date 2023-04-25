<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\CaseHasOffence;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\Court;
use App\Models\Enterprise;
use App\Models\Exhibit;
use App\Models\Image;
use App\Models\ImplementType;
use App\Models\Offence;
use App\Models\PA;
use App\Models\SuspectHasOffence;
use App\Models\Utils;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\TryCatch;

class ApiPostsController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update_profile(Request $r)
    {

        $u = auth('api')->user();

        if ($u == null) {
            return $this->error('Failed to authenticate, please try again.');
        }
        $_u = Administrator::find($u->id);
        if ($u == null) {
            return $this->error('Account not found., please try again.');
        }

        if ($r->first_name == null) {
            return $this->error('You  must provide your first name.');
        }
        if ($r->last_name == null) {
            return $this->error('You  must provide your last name.');
        }

        $_u->first_name = $r->first_name;
        $_u->last_name = $r->last_name;
        $_u->middle_name = $r->middle_name;
        if ($r->sub_county_id != null) {
            $_u->sub_county_id = $r->sub_county_id;
        }
        $_u->phone_number_1 = $r->phone_number_1;
        $_u->phone_number_2 = $r->phone_number_2;
        $_u->address = $r->address;

        try {
            $_u->save();
        } catch (\Throwable $th) {
            return $this->error('Something went wrong.' . $th);
        }


        return $this->success($_u, $message = "Profile updated details", 200);
    }



    public function password_change(Request $r)
    {

        $u = auth('api')->user();

        if ($u == null) {
            return $this->error('Failed to authenticate, please try again.');
        }
        $_u = Administrator::find($u->id);
        if ($u == null) {
            return $this->error('Account not found., please try again.');
        }

        if ($r->current_password == null) {
            return $this->error('Current password is required.');
        }
        if ($r->password == null) {
            return $this->error('New password is required.');
        }

        $current_password = $r->current_password;
        if (!password_verify($current_password, $_u->password)) {
            return $this->error('You entered wrong current password.');
        }



        $_u->password = password_hash($r->password, PASSWORD_DEFAULT);

        try {
            $_u->save();
        } catch (\Throwable $th) {
            return $this->error('Something went wrong.' . $th);
        }


        return $this->success($_u, $message = "Password updated details", 200);
    }





    public function index(Request $r)
    {
        $data =  CaseModel::where([])
        ->with('suspects','exhibits')
        ->orderBy('id', 'Desc')
        ->limit(25)->get(); 
        return $this->success($data, 'Success.');
    }

    public function protected_areas(Request $r)
    {
        $data =  PA::where([])->get();
        return $this->success($data, 'Protected areas.');
    }

    public function animals(Request $r)
    {
        $data =  Animal::where([])->get();
        return $this->success($data, 'Animals.');
    }

    public function implements(Request $r)
    {
        $data =  ImplementType::where([])->get();
        return $this->success($data, 'implements.');
    }

    public function conservation_areas(Request $r)
    {
        $data =  ConservationArea::where([])->get();
        return $this->success($data, 'conservation_areas.');
    }

    public function offences(Request $r)
    {
        $data =  Offence::where([])->get();
        return $this->success($data, 'Offences');
    }

    public function courts(Request $r)
    {
        $data =  Court::where([])->get();
        return $this->success($data, 'courts');
    }
    public function create_post(Request $r)
    {





        $u = auth('api')->user();
        $administrator_id = $u->id;
        $u = Administrator::find($administrator_id);
        if ($u == null) {
            return $this->error('User not found.');
        }

        if ($r->case == null) {
            return $this->error('Case is required.');
        }
        $case_data = json_decode($r->case);

        if ($case_data == null) {
            return $this->error('Fialed to parse Case.');
        }

        $case = null;
        if (isset($case_data->online_id)) {
            //$case = CaseModel::find(((int)($case_data->online_id)));
        }

        if ($case == null) {
            $case = new CaseModel();
            $case->reported_by = $u->id;
        }

        $ignore = [
            'id',
            'created_at',
            'updated_at',
            'reported_by',
            'deleted_at',
        ];
        foreach (Schema::getColumnListing('case_models') as $key) {
            if (in_array($key, $ignore)) {
                continue;
            }
            if (isset($case_data->$key)) {
                $case->$key = $case_data->$key;
            }
        }

        $case->user_adding_suspect_id = null;
        $case->case_submitted = '1';

        try {
            $case->save();
        } catch (\Throwable $th) {
            return $this->error('Failed to update case, because .' . $th->getPrevious()->getMessage());
        }


        $suspects = [];
        $exhibits = [];
        if (isset($r->suspects)) {
            $suspects = json_decode($r->suspects);
            if ($suspects == null) {
                $suspects = [];
            }
        }

        foreach ($suspects as $key => $v) {

            $s = null;
            if (isset($v->online_id)) {
                //$s = CaseSuspect::find(((int)($v->online_id)));
            }

            if ($s == null) {
                $s = new CaseSuspect();
            }




            foreach (Schema::getColumnListing('case_suspects') as $key) {
                if ($v->deleted_at != null) {
                    if (strlen($v->deleted_at) > 3) {
                        $s->photo = 'images/'.$v->deleted_at;
                    }
                }

                if (in_array($key, $ignore)) {
                    continue;
                }
                if (isset($v->$key)) {
                    $s->$key = $v->$key;
                }
            }


          

            $s->uwa_suspect_number = $v->uwa_suspect_number;
            $s->case_id = $case->id;


            try {
                $s->save();
            } catch (\Throwable $th) {
            }


            $offences_ids = [];
            try {
                $offences_ids = json_decode($v->offences_ids);
            } catch (\Throwable $th) {
                $offences_ids = [];
            }

            if ($offences_ids != null) {
                if (is_array($offences_ids)) {
                    foreach ($offences_ids as $offence_id) {
                        $offence = new SuspectHasOffence();
                        $offence->case_suspect_id = $s->id;
                        $offence->offence_id = ((int)($offence_id));
                        $offence->save();
                    }
                }
            }
        }


        $ignore = [
            'id',
            'created_at',
            'updated_at',
            'reported_by',
            'deleted_at',
        ];
        if (isset($r->exhibits)) {
            $exhibits = json_decode($r->exhibits);
            if ($exhibits == null) {
                $exhibits = [];
            }
        }

        foreach ($exhibits as $key => $v) {
            $e = new Exhibit();
            foreach (Schema::getColumnListing('exhibits') as $key) {
                if (in_array($key, $ignore)) {
                    continue;
                }
                if (isset($v->$key)) {
                    $e->$key = $v->$key;
                }
            }

            $type = $e->type_wildlife;

            $imgs = [];
            $d = json_decode($e->attachment);
            if ((is_array($d))) {
                foreach ($d as $c) {
                    $imgs[] =  'images/'.str_replace('"', '', $c);
                }
            }

            if ($e->type_wildlife == 'Other') {
                $e->type_other = 'Yes';
                $e->others_attachments = $imgs;
            } else {
                $e->type_other = 'No';
            }

            if ($e->type_wildlife == 'Implement') {
                $e->type_implement = 'Yes';
                $e->implement_attachments = $imgs;
            } else {
                $e->type_implement = 'No';
            }

            if ($e->type_wildlife == 'Wildlife') {
                $e->type_implement = 'Yes';
                $e->wildlife_attachments = $imgs;
            } else {
                $e->type_implement = 'No';
            }
            $e->case_id = $case->id;

            
            $e->save();
        }



        return $this->success(null, 'Case submitted successfully.');
    }

    public function upload_media(Request $request)
    {

        $u = auth('api')->user();
        $administrator_id = $u->id;
        $u = Administrator::find($administrator_id);
        if ($u == null) {
            return $this->error('User not found.');
        }

        $images = Utils::upload_images_1($_FILES, false);
        $_images = [];
        foreach ($images as $src) {
            $img = new Image();
            $img->administrator_id =  $administrator_id;
            $img->src =  $src;
            $img->thumbnail =  null;
            $img->parent_id =  null;
            $img->size = filesize(Utils::docs_root() . '/storage/images/' . $img->src);
            $img->save();

            $_images[] = $img;
        }
        Utils::process_images_in_backround();

        return $this->success($_images, 'File uploaded successfully.');
    }

    public function process_pending_images()
    {
        Utils::process_images_in_foreround();
        return 1;
    }
}
