<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseHasOffence;
use App\Models\CaseModel;
use App\Models\CaseSuspect;
use App\Models\ConservationArea;
use App\Models\Enterprise;
use App\Models\Exhibit;
use App\Models\Image;
use App\Models\Offence;
use App\Models\PA;
use App\Models\SuspectHasOffence;
use App\Models\Utils;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
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
        $data =  CaseModel::where([])->with('suspects')->limit(25)->get();
        return $this->success($data, 'Success.');
    }

    public function protected_areas(Request $r)
    {
        $data =  PA::where([])->get();
        return $this->success($data, 'Protected areas.');
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



        $case->latitude = $case_data->latitude;
        $case->title = $case_data->title;
        $case->longitude = $case_data->longitude;
        $case->sub_county_id = $case_data->sub_county_id;
        $case->parish = $case_data->parish;
        $case->village = $case_data->village;
        $case->offence_description = $case_data->offence_description;
        $case->is_offence_committed_in_pa = $case_data->is_offence_committed_in_pa;
        $case->pa_id = $case_data->pa_id;
        $case->ca_id = $case_data->ca_id;
        $case->detection_method = $case_data->detection_method;
        $case->offence_category_id = ((int)($case_data->offence_category_id));


        if (!$case->save()) {
            return $this->error('Failed to update case, please try again.');
        }


        $suspects = [];
        $exhibits = [];
        if (isset($r->suspects)) {
            $suspects = json_decode($r->suspects);
            if ($suspects == null) {
                $suspects = [];
            }
        }

        if (isset($r->exhibits)) {
            $exhibits = json_decode($r->exhibits);
            if ($exhibits == null) {
                $exhibits = [];
            }
        }

        foreach ($exhibits as $key => $v) {


            $e = null;
            if (isset($v->online_id)) {
                $e = Exhibit::find(((int)($v->online_id)));
            }

            if ($e == null) {
                $e = new Exhibit();
            }



            $e->case_id = $case->id;
            $e->exhibit_catgory = $v->exhibit_catgory;
            $e->wildlife = '';
            $e->implements = '';
            $e->photos = $v->online_image_ids;
            $e->description = $v->description;
            $e->quantity = ((int)($v->quantity));

            $e->save();
        }


        foreach ($suspects as $key => $v) {

            $s = null;
            if (isset($v->online_id)) {
                //$s = CaseSuspect::find(((int)($v->online_id)));
            }

            if ($s == null) {
                $s = new CaseSuspect();
            }


            $s->uwa_suspect_number = $v->uwa_suspect_number;
            $s->case_id = $case->id;
            $s->first_name = $v->first_name;
            $s->middle_name = $v->middle_name;
            $s->last_name = $v->last_name;
            $s->phone_number = $v->phone_number;
            $s->national_id_number = $v->national_id_number;
            $s->sex = $v->sex;
            if (strlen($v->age) > 3) {
                $s->age = Carbon::parse($v->age);
            }
            if (strlen($v->arrest_date_time) > 3) {
                $s->arrest_date_time = Carbon::parse($v->arrest_date_time);
            }


            $s->occuptaion = $v->occuptaion;
            $s->country = $v->country;
            $s->district_id = $v->district_id;
            $s->sub_county_id = $v->sub_county_id;
            $s->parish = $v->parish;
            $s->sub_county_id = $v->sub_county_id;
            $s->village = $v->village;
            $s->ethnicity = $v->ethnicity;
            $s->finger_prints = $v->finger_prints;
            $s->is_suspects_arrested = $v->is_suspects_arrested;

            $s->arrest_district_id = $v->arrest_district_id;
            $s->arrest_sub_county_id = $v->arrest_sub_county_id;
            $s->arrest_parish = $v->arrest_parish;
            $s->arrest_village = $v->arrest_village;
            $s->arrest_latitude = $v->arrest_latitude;
            $s->arrest_longitude = $v->arrest_longitude;
            $s->arrest_first_police_station = $v->arrest_first_police_station;
            $s->arrest_current_police_station = $v->arrest_current_police_station;
            $s->arrest_agency = $v->arrest_agency;
            $s->arrest_uwa_unit = $v->arrest_uwa_unit;
            $s->arrest_detection_method = $v->arrest_detection_method;
            $s->arrest_uwa_number = $v->arrest_uwa_number;
            $s->arrest_crb_number = $v->arrest_crb_number;
            $s->is_suspect_appear_in_court = $v->is_suspect_appear_in_court;
            $s->prosecutor = $v->prosecutor;
            $s->case_outcome = $v->case_outcome;
            $s->magistrate_name = $v->magistrate_name;
            $s->court_name = isset($v->court_name) ? $v->court_name : "";
            $s->court_file_number = isset($v->court_file_number) ? $v->court_file_number : "";
            $s->is_jailed = isset($v->is_jailed) ? $v->is_jailed : "";
            $s->jail_period = ((int)($v->jail_period));
            $s->is_fined = $v->is_fined;
            $s->fined_amount = ((int)($v->fined_amount));
            $s->status = ((int)($v->status));

            $s->court_date = $v->court_date;
            $s->jail_date = $v->jail_date;
            $s->use_same_arrest_information = $v->use_same_arrest_information;
            $s->use_same_court_information = $v->use_same_court_information;
            $s->suspect_number = $v->suspect_number;
            $s->arrest_in_pa = $v->arrest_in_pa;
            $s->pa_id = $v->pa_id;
            $s->management_action = $v->management_action;
            $s->community_service = $v->community_service;
            $s->reported_by = $v->reported_by;
            $s->add_more_suspects = $v->add_more_suspects;
            $s->ca_id = $v->ca_id;
            $s->not_arrested_remarks = $v->not_arrested_remarks;
            $s->police_sd_number = $v->police_sd_number;
            $s->police_action = $v->police_action;
            $s->police_action_date = $v->police_action_date;
            $s->police_action_remarks = $v->police_action_remarks;
            $s->court_file_status = $v->court_file_status;
            $s->court_status = $v->court_status;
            $s->suspect_court_outcome = $v->suspect_court_outcome;

            $s->save();


            $offence_ids = [];
            try {
                $offence_ids = json_decode($v->offence_ids);
            } catch (\Throwable $th) {
                $offence_ids = [];
            }

            if ($offence_ids != null) {
                if (is_array($offence_ids)) {
                    foreach ($offence_ids as $offence_id) {
                        $offence = new SuspectHasOffence();
                        $offence->case_suspect_id = $s->id;
                        $offence->offence_id = ((int)($offence_id));
                        $offence->save();
                    }
                }
            }
        }


        return $this->success($case, 'Case submitted successfully.');
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
