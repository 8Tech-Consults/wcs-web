<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\Enterprise;
use App\Models\Image;
use App\Models\Utils;
use App\Traits\ApiResponser;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Case_;

class ApiPostsController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index(Request $r)
    {
        $data =  CaseModel::where([])->with('images')->get();
        return $this->success($data, 'Case submitted successfully.');
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
            $case = CaseModel::find(((int)($case_data->online_id)));
        }
        
        if($case == null){
            $case = new CaseModel();
            $case->reported_by = $u->id;
        }
        $case->latitude = $case_data->latitude;
        $case->longitude = $case_data->longitude; 
        $case->sub_county_id = $case_data->sub_county_id;  
        $case->parish = $case_data->parish;  
        $case->village = $case_data->village;  
        $case->offence_category_id = $case_data->offence_category_id;  
        $case->offence_description = $case_data->offence_description;  
        $case->is_offence_committed_in_pa = $case_data->is_offence_committed_in_pa;  
        $case->pa_id = $case_data->pa_id;  
        $case->has_exhibits = $case_data->has_exhibits;  
        $case->status = $case_data->status;     
 

        return $this->success($case, 'Case.');

        if ($r->category == null) {
            return $this->error('Category is required.');
        }

        if ($r->details == null) {
            return $this->error('Description is required.');
        }

        $c = new CaseModel();
        $c->administrator_id  = $administrator_id;
        $c->title  = $r->title;
        $c->latitude  = $r->latitude;
        $c->longitude  = $r->longitude;
        $c->description  = $r->details;
        $c->district  = 1;
        $c->status  = 0;
        $c->sub_county  = 1;
        $c->case_category_id  = 1;
        $c->response  = null;

        if ($c->save()) {

            $imgs =  Image::where([
                'administrator_id' => $administrator_id,
                'parent_id' => null
            ])->get();

            foreach ($imgs as $key => $img) {
                $img->parent_id = $c->id;
                $img->save();
            }
            return $this->success([], 'Case submitted successfully.');
        } else {
            return $this->error('Filed to submit the case.');
        }

        die($u->name);
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
        foreach ($images as $src) {
            $img = new Image();
            $img->administrator_id =  $administrator_id;
            $img->src =  $src;
            $img->thumbnail =  null;
            $img->parent_id =  null;
            $img->size = filesize(Utils::docs_root() . '/storage/images/' . $img->src);
            $img->save();
        }
        Utils::process_images_in_backround();

        return $this->success($images, 'File uploaded successfully.');
    }

    public function process_pending_images()
    {
        Utils::process_images_in_foreround();
        return 1;
    }
}
