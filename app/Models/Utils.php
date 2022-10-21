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


class Utils  extends Model
{

    public static function to_date_time($raw)
    { 
        return Utils::my_date_time($raw);
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
        if($t == null){
            return $t;
        }
        return $c->format('d M, Y');
    }

    public static function my_date_time($t)
    {
        $c = Carbon::parse($t);
        if($t == null){
            return $t;
        }
        return $c->format('d M, Y - h:m a');
    }

    public static function tell_case_status($status)
    {
        if ($status == 1) {
            return 'Draft';
        } else if ($status == 1) {
            return 'Pending for verification';
        } else if ($status == 1) {
            return 'Active';
        } else if ($status == 1) {
            return 'Closed';
        } else {
            return 'Draft';
        }
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
        return [
            null,
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
            "Congo",
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
            "Kenya",
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
            "Rwanda",
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
            "Somalia",
            "South Africa",
            "S. Georgia and S. Sandwich Isls.",
            "Spain",
            "Sri Lanka",
            "St. Helena",
            "St. Pierre and Miquelon",
            "Sudan",
            "Suriname",
            "Svalbard and Jan Mayen Islands",
            "Swaziland",
            "Sweden",
            "Switzerland",
            "Syria",
            "Taiwan",
            "Tajikistan",
            "Tanzania",
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
            "Uganda",
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
        ];
    }
}
