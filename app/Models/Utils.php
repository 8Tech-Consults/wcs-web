<?php

namespace App\Models;

use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Admin;
use Exception;
use Hamcrest\Arrays\IsArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Zebra_Image;
use Faker\Factory as Faker;

class Utils  extends Model
{


    public static function getCaseNumber($case)
    {
        /* foreach (PA::all() as $key => $pa) {
            $pa->short_name = strtoupper(substr($pa->name,0,4));
            $pa->save(); 
            # code...
        } */
        if ($case == null) {
            return "-";
        }

        $case_number = "UWA";
        $pa_found = false;
        if (
            $case->is_offence_committed_in_pa == 1 ||
            $case->is_offence_committed_in_pa == 'Yes'
        ) {
            $pa = PA::find($case->pa_id);
            if ($pa != null) {
                $case_number .= "/{$pa->ca->name}";
                $pa_found = true;
            }
        } else {
            $pa = PA::find(1);
            if ($pa != null) {
                $case_number .= "/{$pa->ca->name}";
                $pa_found = true;
            }
        }


        if (!$pa_found) {
            $case_number = "/-";
        }
        $case_number .= "/" . date('Y');
        $case_number .= "/" . $case->id;
        $case_number = strtoupper($case_number);

        return $case_number;
    }
    public static function system_boot($u)
    {
        ini_set('memory_limit', '-1');


        /*      foreach (CaseSuspect::all() as $key => $c) {
            $c->arrest_latitude = '0.00000';
            $c->arrest_longitude = '0.00000';
            $c->save();
            echo ($c->arrest_in_pa . "<hr>");
        }
        die("");  
 
        $case = CaseSuspect::find(2777);
        $case->arrest_latitude = '0.0000000';
        $case->arrest_longitude = '0.0000000';
        $case->save();
        dd($case->arrest_in_pa);  */
        /*
        $faker = Faker::create();
        foreach (CaseModel::all() as $key => $v) {
            $v->created_at = $faker->dateTimeBetween('-13 month', '+1 month');
            $v->updated_at = $faker->dateTimeBetween('-13 month', '+1 month');
            $v->save();
        }

        foreach (CaseSuspect::all() as $key => $v) {
            $v->created_at = $faker->dateTimeBetween('-13 month', '+1 month');
            $v->updated_at = $faker->dateTimeBetween('-13 month', '+1 month');
            $v->save();
        } */


        $sus = CaseSuspect::where('suspect_number', 'like', '%//%')->get();
        foreach ($sus as $key => $sus) {
            $sus->suspect_number = str_replace('//', '/', $sus->suspect_number);
            $sus->save();
        }



        Utils::copyPendingArrestInfo();
        Utils::copyPendingCourtInfo();
        Utils::copyOffencesCourtInfo();

        $cases = CaseSuspect::where([
            'reported_by' => null
        ])->get();
        foreach ($cases as $key => $sus) {
            if ($sus->case != null) {
                $sus->reported_by = $sus->case->reported_by;
                $sus->save();
            }
        }


        foreach (CaseSuspect::where([
            'ca_id' => null
        ])->get() as $key => $sus) {
            if ($sus->case != null) {
                $sus->ca_id = $sus->case->ca_id;
                $sus->save();
            }
        }

        $cases = Exhibit::where([
            'reported_by' => null
        ])->get();
        foreach ($cases as $key => $sus) {
            try {
                if ($sus->case_model != null) {
                    $sus->reported_by = $sus->case_model->reported_by;
                    $sus->save();
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $cases = CaseModel::where([
            'case_number' => null
        ])->get();

        /*    foreach ($cases as $key => $case) {

            $pa = PA::find($case->pa_id);
            if (
                $pa != null
            ) {
                $case->is_offence_committed_in_pa = 'Yes';
                $case->ca_id = $pa->ca_id;
                $case->save();
            } else {
                $case->is_offence_committed_in_pa = 'No';
                $case->pa_id = 1;
                $case->ca_id = 1;
                $case->save();
            }

            $case->case_number = Utils::getCaseNumber($case);
            $case->save();
        } */

        foreach (CaseSuspect::where([
            'suspect_number' => null
        ])->get() as $key => $suspect) {
            //suspect_number
            if ($suspect->case != null) {
                $suspect->is_suspects_arrested = $suspect->case->case_number . "/" . $suspect->id;
                $suspect->save();
            }
        }
    }

    public static function copyOffencesCourtInfo()
    {

        $suspects = CaseSuspect::where('use_offence_suspect_coped', null)->get();

        foreach ($suspects as $key => $sus) {
            $originalSuspect = CaseSuspect::find($sus->use_offence_suspect_id);
            if ($originalSuspect == null) {
                $sus->use_offence_suspect_coped = 'Yes';
                $sus->use_offence = 'No';
                $sus->save();
                continue;
            }
            $sus->copyOffencesInfo($originalSuspect);
        }
    }


    public static function copyPendingCourtInfo()
    {
        $suspects = CaseSuspect::where('use_same_court_information_coped', null)->get();
        foreach ($suspects as $key => $sus) {
            $originalSuspect = CaseSuspect::find($sus->use_same_court_information_id);
            if ($originalSuspect == null) {
                $sus->use_same_court_information_coped = 'Yes';
                $sus->use_same_court_information = 'No';
                $sus->save();
                continue;
            }
            $sus->copyCourtInfo($originalSuspect);
        }
    }

    public static function copyPendingArrestInfo()
    {
        $suspects = CaseSuspect::where('use_same_arrest_information_coped', null)->get();
        foreach ($suspects as $key => $sus) {

            $originalSuspect = CaseSuspect::find($sus->use_same_arrest_information_id);
            if ($originalSuspect == null) {
                $sus->use_same_arrest_information_coped = 'Yes';
                $sus->use_same_arrest_information = 'No';
                $sus->save();
                continue;
            }
            $sus->copyArrestInfo($originalSuspect);
        }
    }

    public static function hasPendingCase($u)
    {

        $case = CaseModel::where(['user_adding_suspect_id' => Auth::user()->id])->orderBy('id', 'desc')->first();

        if ($case != null) {
            return $case;
        }

        if (isset($_GET['add_suspect_to_case_id'])) {
            $case_id = ((int)($_GET['add_suspect_to_case_id']));
            $case = CaseModel::find($case_id);
            if ($case != null) {
                $case->user_adding_suspect_id = Auth::user()->id;
                $case->save();
                return $case;
            }
        }

        //new changes
        $case =  CaseModel::where([
            'case_submitted' => null,
            "reported_by" => $u->id
        ])
            ->orderBy('id', 'Desc')
            ->first();
        if ($case == null) {
            $case =  CaseModel::where([
                'case_submitted' => '0',
                "reported_by" => $u->id
            ])
                ->orderBy('id', 'Desc')
                ->first();
        }

        if ($case == null) {
            return null;
        }

        if ($case->exhibits != null) {
            if (count($case->exhibits) > 0) {
                if ($case->case_step < 3) {
                    $case->case_step = 3;
                    $case->save();
                }
            }
        }
        return $case;

        $sql = DB::select("SELECT * FROM case_models WHERE reported_by = {$u->id} AND (SELECT count(id) FROM case_suspects WHERE case_id = case_models.id) < 1");
        if (count($sql) > 0) {
            $case = CaseModel::find($sql[0]->id);
            return $case;
        }
        return null;

        $case = CaseModel::where([
            'done_adding_suspects' => null,
            "reported_by" => $u->id
        ])->first();

        return $case;
    }
    public static function get($class, $id)
    {
        $data = $class::find($id);
        if ($data != null) {
            return $data;
        }
        return new $class();
    }
    public static function to_date_time($raw)
    {
        return Utils::my_date_time($raw);
    }

    public static function get_edit_suspect()
    {
        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $pendingCase = null;
        $ex = CaseSuspect::find($arr[2]);
        if ($ex == null) {
            foreach ($arr as $key => $val) {
                $ex = CaseSuspect::find($val);
                if ($ex != null) {
                    break;
                }
            }
        }
 
        return $ex;
    }

    public static function get_edit_case()
    {
        $arr = (explode('/', $_SERVER['REQUEST_URI']));
        $pendingCase = null;
        $ex = CaseSuspect::find($arr[2]);
        if ($ex == null) {
            foreach ($arr as $key => $val) {
                $ex = CaseSuspect::find($val);
                if ($ex != null) {
                    break;
                }
            }
        }

        $pendingCase = null; 
        if($ex != null){
            $pendingCase = CaseModel::find($ex->case_id);
        }
        return $pendingCase;
    }
    public static function docs_root($params = array())
    {
        $r = $_SERVER['DOCUMENT_ROOT'] . "";
        $r = str_replace('/public', "", $r);
        $r = $r . "/public";
        return $r;
    }



    public static function create_thumbail($params = array())
    {

        ini_set('memory_limit', '-1');

        if (
            !isset($params['source']) ||
            !isset($params['target'])
        ) {
            return [];
        }

        $image = new Zebra_Image();

        $image->auto_handle_exif_orientation = false;
        $image->source_path = "" . $params['source'];
        $image->target_path = "" . $params['target'];


        if (isset($params['quality'])) {
            $image->jpeg_quality = $params['quality'];
        }

        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $image->handle_exif_orientation_tag = true;

        $img_size = getimagesize($image->source_path); // returns an array that is filled with info

        $width = 300;
        $heigt = 300;

        if (isset($img_size[0]) && isset($img_size[1])) {
            $width = $img_size[0];
            $heigt = $img_size[1];
        }
        //dd("W: $width \n H: $heigt");

        if ($width < $heigt) {
            $heigt = $width;
        } else {
            $width = $heigt;
        }

        if (isset($params['width'])) {
            $width = $params['width'];
        }

        if (isset($params['heigt'])) {
            $width = $params['heigt'];
        }

        $image->jpeg_quality = 50;
        $image->jpeg_quality = Utils::get_jpeg_quality(filesize($image->source_path));
        if (!$image->resize($width, $heigt, ZEBRA_IMAGE_CROP_CENTER)) {
            return $image->source_path;
        } else {
            return $image->target_path;
        }
    }

    public static function get_jpeg_quality($_size)
    {
        $size = ($_size / 1000000);

        $qt = 50;
        if ($size > 5) {
            $qt = 10;
        } else if ($size > 4) {
            $qt = 13;
        } else if ($size > 2) {
            $qt = 15;
        } else if ($size > 1) {
            $qt = 17;
        } else if ($size > 0.8) {
            $qt = 50;
        } else if ($size > .5) {
            $qt = 80;
        } else {
            $qt = 90;
        }

        return $qt;
    }

    public static function process_images_in_backround()
    {
        $url = url('api/process-pending-images');
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        try {
            $data =  file_get_contents($url, null, $ctx);
            return $data;
        } catch (Exception $x) {
            return "Failed $url";
        }
    }

    public static function process_images_in_foreround()
    {
        $imgs = Image::where([
            'thumbnail' => null
        ])->get();

        foreach ($imgs as $img) {
            $thumb = Utils::create_thumbail([
                'source' => Utils::docs_root() . '/storage/images/' . $img->src,
                'target' => Utils::docs_root() . '/storage/images/thumb_' . $img->src,
            ]);
            if ($thumb != null) {
                if (strlen($thumb) > 4) {
                    $img->thumbnail = $thumb;
                    $img->save();
                }
            }
        }
    }




    public static function upload_images_1($files, $is_single_file = false)
    {

        ini_set('memory_limit', '-1');
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
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = time() . "-" . rand(100000, 1000000) . "." . $ext;
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


        return $is_single_file ? $single_file : $uploaded_images;
    }



    public static function number_format($num, $unit)
    {
        $num = (int)($num);
        $resp = number_format($num);
        if ($num < 2) {
            $resp .= " " . $unit;
        } else {
            $resp .= " " . Str::plural($unit);
        }
        return $resp;
    }

    static function unzip(string $zip_file_path, string $extract_dir_path)
    {
        $zip = new \ZipArchive;
        $res = $zip->open($zip_file_path);
        if ($res == TRUE) {
            $zip->extractTo($extract_dir_path);
            $zip->close();
            return TRUE;
        } else {
            return FALSE;
        }
    }



    public static function my_date($t)
    {
        $c = Carbon::parse($t);
        $c->setTimezone(config('app.timezone'));

        if ($t == null) {
            return $t;
        }
        return $c->format('d M, Y');
    }

    public static function month($t)
    {
        $c = Carbon::parse($t);
        $c->setTimezone(config('app.timezone'));

        if ($t == null) {
            return $t;
        }
        return $c->format('M - Y');
    }

    public static function my_time_ago($t)
    {
        $c = Carbon::parse($t);
        $c->setTimezone(config('app.timezone'));

        if ($t == null) {
            return $t;
        }
        return $c->diffForHumans();
    }

    public static function my_date_time($t)
    {
        $c = Carbon::parse($t);
        $c->setTimezone(config('app.timezone'));
        if ($t == null) {
            return $t;
        }
        return $c->format('d M, Y - h:m a');
    }

    public static function tell_suspect_status($s)
    {
        if($s->is_suspect_appear_in_court == null || $s->is_suspect_appear_in_court == 'No'){
            if($s->is_suspects_arrested == 'Yes'){
                return 'At Police';
            }else{
                return 'Not At Police';
            }
        }
        else {
            return $s->court_status;
        }
    }

    public static function tell_suspect_status_color($s)
    {
        if($s->is_suspect_appear_in_court == null || $s->is_suspect_appear_in_court == 'No'){
            if($s->is_suspects_arrested == 'Yes'){
                return 'warning';
            }else{
                return 'info';
            }
        }
        else {
            if($s->court_status == 'Concluded') {
                return 'danger';
            }else if($s->court_status == 'On-going prosecution') {
                return 'success';
            }else {
                return 'primary';
            }
        }
    }

    public static function tell_case_status($status)
    {
        if ($status == 1) {
            return 'On-going';
        } else if ($status == 2) {
            return 'Closed';
        } else if ($status == 3) {
            return 'Re-opened';
        } else {
            return 'Closed';
        }
    }

    public static function tell_case_status_color($status)
    {
        if ($status == 1) {
            return 'warning';
        } else if ($status == 2) {
            return 'success';
        } else if ($status == 3) {
            return 'danger';
        } else {
            return 'danger';
        }
    }
    public static function get_gps_link($latitude, $longitude)
    {
        return '<a target="_blank" href="https://www.google.com/maps/dir/' .
            $latitude .
            ",{$longitude}" .
            '">View location on map</a>';
    }

    public static function phone_number_is_valid($phone_number)
    {
        $phone_number = Utils::prepare_phone_number($phone_number);
        if (substr($phone_number, 0, 4) != "+256") {
            return false;
        }

        if (strlen($phone_number) != 13) {
            return false;
        }

        return true;
    }
    public static function prepare_phone_number($phone_number)
    {

        if (strlen($phone_number) == 14) {
            $phone_number = str_replace("+", "", $phone_number);
            $phone_number = str_replace("256", "", $phone_number);
        }


        if (strlen($phone_number) > 11) {
            $phone_number = str_replace("+", "", $phone_number);
            $phone_number = substr($phone_number, 3, strlen($phone_number));
        } else {
            if (strlen($phone_number) == 10) {
                $phone_number = substr($phone_number, 1, strlen($phone_number));
            }
        }


        if (strlen($phone_number) != 9) {
            return "";
        }

        $phone_number = "+256" . $phone_number;
        return $phone_number;
    }


    public static function COUNTRIES()
    {
        $data = [];
        foreach ([
            '',
            "Kenya",
            "Tanzania",
            "Rwanda",
            "Congo",
            "Somalia",
            "Sudan",
            "Afghanistan",
            "Albania",
            "Algeria",
            "American Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antarctica",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bosnia and Herzegovina",
            "Botswana",
            "Bouvet Island",
            "Brazil",
            "British Indian Ocean Territory",
            "Brunei Darussalam",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Cape Verde",
            "Cayman Islands",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Christmas Island",
            "Cocos (Keeling Islands)",
            "Colombia",
            "Comoros",
            "Cook Islands",
            "Costa Rica",
            "Cote D'Ivoire (Ivory Coast)",
            "Croatia (Hrvatska",
            "Cuba",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "East Timor",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Ethiopia",
            "Falkland Islands (Malvinas)",
            "Faroe Islands",
            "Fiji",
            "Finland",
            "France",
            "France",
            "Metropolitan",
            "French Guiana",
            "French Polynesia",
            "French Southern Territories",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Gibraltar",
            "Greece",
            "Greenland",
            "Grenada",
            "Guadeloupe",
            "Guam",
            "Guatemala",
            "Guinea",
            "Guinea-Bissau",
            "Guyana",
            "Haiti",
            "Heard and McDonald Islands",
            "Honduras",
            "Hong Kong",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran",
            "Iraq",
            "Ireland",
            "Israel",
            "Italy",
            "Jamaica",
            "Japan",
            "Jordan",
            "Kazakhstan",

            "Kiribati",
            "Korea (North)",
            "Korea (South)",
            "Kuwait",
            "Kyrgyzstan",
            "Laos",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Macau",
            "Macedonia",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Martinique",
            "Mauritania",
            "Mauritius",
            "Mayotte",
            "Mexico",
            "Micronesia",
            "Moldova",
            "Monaco",
            "Mongolia",
            "Montserrat",
            "Morocco",
            "Mozambique",
            "Myanmar",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "Netherlands Antilles",
            "New Caledonia",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "Niue",
            "Norfolk Island",
            "Northern Mariana Islands",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Pitcairn",
            "Poland",
            "Portugal",
            "Puerto Rico",
            "Qatar",
            "Reunion",
            "Romania",
            "Russian Federation",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Vincent and The Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Slovak Republic",
            "Slovenia",
            "Solomon Islands",

            "South Africa",
            "S. Georgia and S. Sandwich Isls.",
            "Spain",
            "Sri Lanka",
            "St. Helena",
            "St. Pierre and Miquelon",
            "Suriname",
            "Svalbard and Jan Mayen Islands",
            "Swaziland",
            "Sweden",
            "Switzerland",
            "Syria",
            "Taiwan",
            "Tajikistan",
            "Thailand",
            "Togo",
            "Tokelau",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Turks and Caicos Islands",
            "Tuvalu",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom (Britain / UK)",
            "United States of America (USA)",
            "US Minor Outlying Islands",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Vatican City State (Holy See)",
            "Venezuela",
            "Viet Nam",
            "Virgin Islands (British)",
            "Virgin Islands (US)",
            "Wallis and Futuna Islands",
            "Western Sahara",
            "Yemen",
            "Yugoslavia",
            "Zaire",
            "Zambia",
            "Zimbabwe"
        ] as $key => $v) {
            $data[$v] = $v;
        };
        return $data;
    }
}
