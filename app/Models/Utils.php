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
        $pa = PA::find($case->pa_id);
        if ($pa != null) {
            $case_number .= "/{$pa->ca->name}";
            $pa_found = true;
            if ($pa->id > 1) {
                $case->is_offence_committed_in_pa == 'Yes';
            } else {
                $case->is_offence_committed_in_pa == 'No';
            }
            if ($pa->id == 1) {
                $ca = ConservationArea::find($case->ca_id);
                if ($ca != null) {
                    if ($ca->id != 1) {
                        $case_number = "UWA/{$ca->name}";
                    }
                }
            }
        }



        /* if (
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
            */
        if (!$pa_found) {
            $case_number = "/-";
        }

        $date = null;
        if ($case->case_date != null) {
            try {
                $date = Carbon::parse($case->case_date);
            } catch (\Throwable $th) {
                $date = null;
            }
        }

        if ($date == null) {
            try {
                $date = Carbon::parse($case->created_at);
            } catch (\Throwable $th) {
                $date = Carbon::now();
            }
        }

        $case_number .= "/" . $date->format('Y');
        $case_number .= "/" . $case->id;
        $case_number = strtoupper($case_number);
        return $case_number;
    }


    public static function import_cases()
    {
        $same_names = [];
        $no_names = [];
        $u = Admin::user();
        if ($u == null) {
            return;
        }
        //set unlimited time
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $base = Utils::docs_root();
        //csv file
        $csv =  $base . '/cases.csv';
        //check if file exists
        if (!file_exists($csv)) {
            die("cases file not found.");
            return;
        }

        //read from file and loop 
        $i = 0;
        $isFirst = false;
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!$isFirst) {
                    $isFirst = true;
                    continue;
                }
                $key = 0;
                $word = trim($data[$key]);
                if (!isset($data[$key])) {
                    die("No data found in column $key");
                }

                $title = trim($data[3]);
                if (strlen($title) < 3) {
                    $no_names[] = $data;
                    continue;
                    die("No title found in column 3");
                }
                $case = CaseModel::where('title', $title)->first();
                if ($case != null) {
                    $same_names[] = $title;
                    //continue;
                } else {
                    continue;
                    //$case = new CaseModel();
                }

                $ca_text = trim($data[4]);
                $ca = ConservationArea::where('name', $ca_text)->first();
                if ($ca == null) {
                    continue;
                }

                if ($ca->id < 2) {
                    continue;
                }

                if ($case->pa_id > 1) {
                    continue;
                }

                $case->ca_id = $ca->id;
                $case->save();
                continue;

                if ($ca_text == 'NAPA') {
                    $ca = ConservationArea::find(1);
                }
                if ($ca == null) {
                    $ca = ConservationArea::find(1);
                }
                $case->created_at = Carbon::parse($data[0]);
                $case->title = $title;
                $case->reported_by = $u->id;
                $case->ca_id = $ca->id;
                $case->conservation_area_id = $ca->id;
                //$case->case_number = Utils::getCaseNumber($case);
                $case->suspect_appealed = $data[5];
                $case->done_adding_suspects = 'Yes';
                try {
                    $case->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
                $i++;
            }
            fclose($handle);
        }
    }

    public static function import_suspects()
    {
        $same_names = [];
        $no_names = [];
        $u = Admin::user();
        if ($u == null) {
            return;
        }
        //set unlimited time
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $base = Utils::docs_root();
        //csv file
        $csv =  $base . '/suspects.csv';
        //check if file exists
        if (!file_exists($csv)) {
            die("cases file not found.");
            return;
        }


        //read from file and loop 
        $i = 0;
        $x = 0;
        $isFirst = false;
        $case_not_found = [];
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000000, ",")) !== FALSE) {
                if (!$isFirst) {
                    $isFirst = true;
                    continue;
                }
                $key = 0;
                $word = trim($data[$key]);
                if (!isset($data[$key])) {
                    die("No data found in column $key");
                }

                $old_supect_number = trim($data[1]);
                if (strlen($old_supect_number) < 2) {
                    //$case_not_found[] = $old_supect_number;
                    //continue;
                    //die("No suspect number found in column 1");
                }

                $case_title = trim($data[5]);
                if (strlen($case_title) < 3) {
                    continue;
                    echo ("FAILED No case title found in column 51 ==> $case_title");
                }

                $case = CaseModel::where('title', $case_title)->first();
                if ($case == null) {
                    continue;
                    die("No case found with title $case_title");
                }


                $first_name = trim($data[18]);
                $last_name = trim($data[19]);
                $old_suspect = CaseSuspect::where([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'case_id' => $case->id,
                ])->first();

                if ($old_suspect == null) {
                    continue;
                }
                $off = [];
                $off[] = $data[32];
                $off[] = $data[33];
                $off[] = $data[34];
                $off[] = $data[35];
                foreach ($off as $key => $of) {
                    if ($of == null) {
                        continue;
                    }
                    if (strlen($of) < 3) {
                        continue;
                    }
                    $offence = Offence::where([
                        'name' => trim($of)
                    ])->first();
                    if ($offence == null) {
                        echo "<code>$of<code><br>";
                        continue;
                    }

                    $has = SuspectHasOffence::where([
                        'case_suspect_id' => $old_suspect->id,
                        'offence_id' => $offence->id,
                    ])->first();
                    if ($has != null) {
                        continue;
                    }
                    $newOff = new SuspectHasOffence();
                    $newOff->case_suspect_id = $old_suspect->id;
                    $newOff->offence_id = $offence->id;
                    $newOff->vadict = null;
                    $newOff->save();
                    echo $newOff->id . " success <br>";
                }
                continue;

                dd('');
                dd($off);

                dd($old_suspect);
                if (strtolower($old_suspect->is_suspect_appear_in_court) != 'yes') {
                    $old_suspect->court_date = null;
                    $old_suspect->save();
                    continue;
                }
                $name = trim($data['66']);

                $old_suspect->court_name = 1;
                if (strlen($name) < 2) {
                } else {
                    $court = Court::where([
                        'name' => $name,
                    ])->first();
                    if ($court == null) {
                        $court = new Court();
                        $court->name = $name;
                        $court->save();
                    }
                    $old_suspect->court_name = $court->id;
                }

                $old_suspect->save();
                continue;

                dd($court);
                dd($name);
                //court_name


                /* 
array:91 [▼
  0 => "﻿Year"
  1 => "UWA Number"
  2 => "Case number"
  3 => "Suspect number"
  4 => "Date"
  5 => "Case title"
  6 => "C.A of case"
  7 => "Complainant"
  8 => "In P.A"
  9 => "P.A of case"
  10 => "C.A of case"
  11 => "Location"
  12 => "District"
  13 => "Sub-county"
  14 => "Parish"
  15 => "Village"
  16 => "GPS"
  17 => "Detection method"
  18 => "Suspect First Name "
  19 => "Suspect Last Name"
  20 => "Sex"
  21 => "Age (years)"
  22 => "Phone number"
  23 => "ID Type"
  24 => "ID Number"
  25 => "Occupation"
  26 => "Nationality"
  27 => "District"
  28 => "Sub County"
  29 => "Parish"
  30 => "Village"
  31 => "Ethnicity"
  32 => "Offence 1"
  33 => "Offence 2"
  34 => "Offence 3"
  35 => "Offence 4"
  36 => "At Police"
  37 => "Managment action"
  38 => "Managment remarks"
  39 => "Arrest date"
  40 => "Arrest in P.A"
  41 => "P.A of Arrest"
  42 => "C.A of Arrest"
  43 => "Arrest Location"
  44 => "District"
  45 => "Sub-county"
  46 => "Arrest parish"
  47 => "Arrest village"
  48 => "Arrest GPS latitude"
  49 => "Arrest GPS longitude"
  50 => "First police station"
  51 => "Current police station"
  52 => "Lead Arrest agency"
  53 => "Arrest uwa unit"
  54 => "Other Arrest Agencies 1"
  55 => "Other Arrest Agencies 2"
  56 => "Other Arrest Agencies 3"
  57 => "Arrest crb number"
  58 => "Police sd number"
  59 => "Appeared Court"
  60 => "Case status"
  61 => "Police action"
  62 => "Police action date"
  63 => "Police remarks"
  64 => "Court file number"
  65 => "Court date"
  66 => "Court name"
  67 => "Lead prosecutor"
  68 => "Magistrate name"
  69 => "Court case status"
  70 => "Accused court status"
  71 => "Specific court case status"
  72 => "Remarks"
  73 => "Jailed"
  74 => "Sentence date"
  75 => "Jail period"
  76 => "Prison"
  77 => "Date release"
  78 => "Accused fined"
  79 => "Fined amount"
  80 => "Community service"
  81 => "Duration (in hours)"
  82 => "Cautioned"
  83 => "Cautioned remarks" 
  85 => "Appeal date"
  86 => "Appellate court name"
  87 => "Appeal court file number"
  88 => "Appeal outcome"
  89 => "Appeal remarks"
  90 => ""
]
                */


                $now = Carbon::parse('2024-01-10');
                $then = Carbon::parse($old_suspect->court_date);
                if (!$now->isBefore($then)) {
                    continue;
                }


                $court_date = trim($data[65]);

                $court_date_1 = null;
                if (strlen($court_date) > 4) {
                    try {
                        $court_date_1 = Carbon::parse(trim($data[65]));
                    } catch (\Throwable $th) {
                        $court_date_1 = null;
                    }
                } else {
                    echo "<br> no date ";
                    continue;
                }
                // dd($court_date_1);
                // dd($old_suspect->court_date);
                $x++;
                if ($x > 1000000) {
                    break;
                }
                $old_suspect->court_date = $court_date_1;
                $old_suspect->save();
                echo "<br>$x. ";
                echo " NEW : {$old_suspect->first_name} {$old_suspect->last_name} . $old_suspect->court_date";

                continue;


                $isEdit = false;
                if ($old_suspect != null) {
                    $sus = $old_suspect;
                    //echo " Found : {$sus->first_name} {$sus->last_name}";
                    continue;
                } else {
                    $sus = new CaseSuspect();
                }
                echo "<br>$x. ";
                echo " NEW : {$old_suspect->first_name} {$old_suspect->last_name}";


                $ca_text = trim($data[6]);
                $ca = ConservationArea::where('name', $ca_text)->first();
                if ($ca == null) {
                    $ca = ConservationArea::find(1);
                }
                $pa = PA::where('name', trim($data[9]))->first();
                if ($pa == null) {
                    $pa = PA::find(1);
                    //die("No PA found with name " . trim($data[8]));
                }


                $sus->old_supect_number = $old_supect_number;
                $sus->case_id = $case->id;
                $sus->created_at = $case->created_at;
                $sus->pa_id = $pa->id;
                $sus->ca_id = $pa->ca_id;
                $sus->created_by_ca_id = $pa->ca_id;
                //$sus->officer_in_charge = trim($data[7]);
                $sus->arrest_village = trim($data[11]);
                $sus->arrest_village = trim($data[11]);
                $sus->arrest_parish = trim($data[11]);
                $sus->parish = trim($data[11]);
                $sus->village = trim($data[11]);
                $district = Location::where('name', trim($data[12]))->first();
                if ($district != null) {
                    $sus->arrest_district_id = $district->id;
                    $sus->district_id = $district->id;
                }
                $sub_county = Location::where('name', trim($data[13]))->first();
                if ($sub_county != null) {
                    $sus->arrest_sub_county_id = $sub_county->id;
                    $sus->sub_county_id = $sub_county->id;
                }

                $prish_text = trim($data[14]);
                if ($prish_text != null && strlen($prish_text) > 3) {
                    $sus->parish = $prish_text;
                }
                $vil_text = trim($data[15]);
                if ($vil_text != null && strlen($vil_text) > 3) {
                    $sus->village = $vil_text;
                }
                $sus->arrest_detection_method = trim($data[17]);
                $sus->first_name = trim($data[18]);
                $sus->last_name = trim($data[19]);
                $sus->sex = trim($data[20]);
                if ($sus->sex != 'Male' && $sus->sex != 'Female') {
                    $sus->sex = 'Male';
                }
                $sus->age = trim($data[21]);
                $sus->phone_number = trim($data[22]);
                $sus->type_of_id = trim($data[23]);
                $sus->national_id_number = trim($data[24]);
                $sus->occuptaion = trim($data[25]);
                $sus->country = trim($data[26]);
                $district = Location::where('name', trim($data[27]))->first();
                if ($district != null) {
                    $sus->arrest_district_id = $district->id;
                    $sus->district_id = $district->id;
                }


                $sub_county = Location::where('name', trim($data[28]))->first();
                if ($sub_county != null) {
                    $sus->arrest_sub_county_id = $sub_county->id;
                    $sus->sub_county_id = $sub_county->id;
                }

                $vil_text = trim($data[29]);
                if ($vil_text != null && strlen($vil_text) > 3) {
                    $sus->parish = $vil_text;
                }
                $village = trim($data[30]);
                if ($village != null && strlen($vil_text) > 3) {
                    $sus->village = $village;
                }
                $sus->ethnicity = trim($data[31]);

                $offences = [];
                $offences[] = trim($data[32]);
                $offences[] = trim($data[33]);
                $offences[] = trim($data[34]);
                $offences[] = trim($data[35]);
                $at_police = 'No';
                $at_police = trim($data[36]);

                if (strtolower($at_police) == 'yes') {
                    $sus->is_suspects_arrested = 'Yes';
                    $sus->arrest_date_time = Carbon::parse(trim($data[39]));
                    $sus->arrest_in_pa = trim($data[40]);
                    $pa = PA::where('name', trim($data[41]))->first();
                    if ($pa != null) {
                        $sus->arrest_in_pa = $pa->id;
                        $sus->ca_id = $pa->ca_id;
                    } else {
                        $pa = PA::find(1);
                        $sus->ca_id = 1;
                    }
                    $sus->arrest_village = trim($data[43]);

                    $district = Location::where('name', trim($data[44]))->first();
                    if ($district != null) {
                        $sus->arrest_district_id = $district->id;
                    }

                    $sub_county = Location::where('name', trim($data[45]))->first();
                    if ($sub_county != null) {
                        $sus->arrest_sub_county_id = $sub_county->id;
                    }
                    $arrest_village = trim($data[46]);
                    if (
                        $arrest_village != null &&
                        strlen($arrest_village) > 2
                    ) {
                        $sus->arrest_village = $arrest_village;
                        $sus->arrest_parish = $arrest_village;
                    }

                    $arrest_parish = trim($data[47]);
                    if (
                        $arrest_parish != null &&
                        strlen($arrest_parish) > 2
                    ) {
                        $sus->arrest_village = $arrest_parish;
                        $sus->arrest_parish = $arrest_parish;
                    }
                    $sus->arrest_first_police_station = trim($data[50]);
                    $sus->arrest_current_police_station = trim($data[51]);
                    $sus->arrest_agency = trim($data[52]);
                    $sus->arrest_uwa_unit = trim($data[53]);
                    $other_arrest_agencies = [];
                    $other_arrest_agencies_1 = trim($data[54]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        $other_arrest_agencies[] = $other_arrest_agencies_1;
                    }
                    $other_arrest_agencies_1 = trim($data[55]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        if (!in_array($other_arrest_agencies_1, $other_arrest_agencies)) {
                            $other_arrest_agencies[] = $other_arrest_agencies_1;
                        }
                    }
                    $other_arrest_agencies_1 = trim($data[56]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        if (!in_array($other_arrest_agencies_1, $other_arrest_agencies)) {
                            $other_arrest_agencies[] = $other_arrest_agencies_1;
                        }
                    }
                    $sus->arrest_crb_number = trim($data[57]);
                    $sus->police_sd_number = trim($data[58]);
                    $sus->is_suspect_appear_in_court = trim($data[59]);
                    $sus->status = trim($data[60]);
                    $sus->police_action = trim($data[61]);
                    $sus->police_action_remarks = trim($data[63]);
                    $sus->suspect_appealed_court_file = trim($data[64]);

                    try {
                        $sus->police_action_date = Carbon::parse(trim($data[62]));
                    } catch (\Throwable $th) {
                        $sus->police_action_date = '';
                    }

                    if (strtolower($sus->is_suspect_appear_in_court) == 'yes') {
                        $sus->is_suspect_appear_in_court = 'Yes';
                        $court_date = trim($data[65]);
                        try {
                            $sus->court_date = Carbon::parse(trim($data[62]));
                        } catch (\Throwable $th) {
                            $sus->court_date = $court_date;
                        }

                        $sus->court_name =  trim($data[66]);
                        $sus->prosecutor =  trim($data[67]);
                        $sus->prosecutor =  trim($data[67]);
                        $sus->magistrate_name =  trim($data[68]);
                        $sus->court_status =  trim($data[69]);
                        $sus->suspect_court_outcome =  trim($data[70]);
                        $sus->case_outcome =  trim($data[71]);
                        $sus->case_outcome_remarks =  trim($data[72]);
                        $is_jailed =  trim($data[73]);
                        if (strtolower($is_jailed) == 'yes') {
                            $sus->is_jailed = 'Yes';
                            $jail_date = trim($data[74]);
                            if (strlen($jail_date) > 2) {
                                try {
                                    $sus->jail_date = Carbon::parse($jail_date);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $sus->jail_date = "";
                                }
                            }
                        } else {
                            $sus->is_jailed = 'No';
                        }
                        $sus->jail_period =  trim($data[75]);
                        $sus->prison =  trim($data[76]);
                        try {
                            if (strlen(trim($data[77])) > 3) {
                                $sus->jail_release_date =  Carbon::parse(trim($data[77]));
                            }
                        } catch (\Throwable $th) {
                            $sus->jail_release_date = "";
                        }
                        $sus->is_fined =  trim($data[78]);
                        if ($sus->is_fined != "Yes") {
                            $sus->is_fined = 'No';
                        }
                        $sus->fined_amount =  trim($data[79]);
                        $sus->community_service =  trim($data[80]);
                        $sus->community_service_duration =  trim($data[81]);
                        $sus->cautioned_remarks =  trim($data[82]);
                        $sus->cautioned =  trim($data[82]);
                        $sus->cautioned_remarks =  trim($data[83]);
                        $sus->suspect_appealed =  trim($data[84]);
                        $sus->suspect_appealed_date =  trim($data[85]);
                        if (strlen($sus->suspect_appealed_date) > 3) {
                            try {
                                $sus->suspect_appealed_date = Carbon::parse($sus->suspect_appealed_date);
                            } catch (\Throwable $th) {
                                $sus->suspect_appealed_date = "";
                            }
                        }
                        $sus->suspect_appealed_court_name =  trim($data[86]);
                        $sus->suspect_appealed_court_file =  trim($data[87]);
                        $sus->suspect_appealed_outcome =  trim($data[88]);
                        $sus->suspect_appeal_remarks =  trim($data[89]);
                    } else {
                        $sus->is_suspect_appear_in_court = 'No';
                    }
                } else {
                    $sus->is_suspects_arrested = 'No';
                }
                $sus->management_action = trim($data[37]);
                $sus->not_arrested_remarks = trim($data[38]);

                $sus->reported_by = 1;
                $sus->save();
                foreach ($offences as $_key => $_val) {
                    $offence = Offence::where('name', $_val)->first();
                    if ($offence == null) {
                        continue;
                    }
                    $old = SuspectHasOffence::where([
                        'offence_id' => $offence->id,
                        'case_suspect_id' => $sus->id,
                    ])->first();
                    if ($old == null) {
                        continue;
                    }
                    $hasOffence = new SuspectHasOffence();
                    $hasOffence->case_suspect_id = $sus->id;
                    $hasOffence->offence_id = $offence->id;
                    $hasOffence->save();
                }
                echo (" | success adding $sus->name");
            }
            fclose($handle);
        }

        echo "<pre>";
        print_r($no_names);
        die();
    }


    public static function import_exhibits()
    {

        $same_names = [];
        $no_names = [];
        $u = Admin::user();
        if ($u == null) {
            return;
        }
        //set unlimited time
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $base = Utils::docs_root();
        //csv file
        $csv =  $base . '/exhibits.csv';
        //check if file exists
        if (!file_exists($csv)) {
            die("cases file not found.");
            return;
        }


        //read from file and loop 
        $i = 0;
        $x = 0;
        $isFirst = false;
        $case_not_found = [];
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000000, ",")) !== FALSE) {
                if (!$isFirst) {
                    $isFirst = true;
                    continue;
                }
                $key = 0;
                $word = trim($data[$key]);
                if (!isset($data[$key])) {
                    die("No data found in column $key");
                }

                $old_supect_number = trim($data[1]);
                if (strlen($old_supect_number) < 2) {
                    //$case_not_found[] = $old_supect_number;
                    //continue;
                    //die("No suspect number found in column 1");
                }

                $case_title = trim($data[2]);
                if (strlen($case_title) < 3) {
                    continue;
                    $no_names[] = $data;
                    echo "<hr><pre>";
                    print_r($data);
                    echo "<hr></pre>";
                    echo ("FAILED No case title found in column 5 ==> $case_title");
                }

                $case = CaseModel::where('title', $case_title)->first();
                if ($case == null) {
                    die("No case found with title $case_title");
                }
                /* 
	updated_at	case_id	exhibit_catgory	wildlife	implements	photos	description	quantity	deleted_at	implement	reported_by	latitude	longitude	attachment	number_of_pieces	ca_id	add_another_exhibit	type_wildlife	type_implement	type_other	pics	wildlife_species	wildlife_quantity	wildlife_desciprion	wildlife_pieces	wildlife_description	wildlife_attachments	implement_name	implement_pieces	implement_description	implement_attachments	others_description	others_attachments	other_wildlife_species	specimen	other_implement	

*/
                $ex = new Exhibit();
                $ex->created_at = Carbon::parse(trim($data[1]));
                $ex->specimen = trim($data[6]);
                $ex->wildlife_quantity = trim($data[7]);
                $ex->wildlife_description = trim($data[9]);
                $ex->implement_pieces = trim($data[11]);
                $ex->implement_description = trim($data[12]);
                $implement_name = ImplementType::where('name', trim($data[10]))->first();
                if ($implement_name != null) {
                    $ex->implement_name = $implement_name->id;
                    $ex->type_implement = 'Yes';
                }
                $wildlife_species = Animal::where('name', trim($data[5]))->first();
                if ($wildlife_species != null) {
                    $ex->wildlife_species = $wildlife_species->id;
                    $ex->type_wildlife = 'Yes';
                }
                $ex->wildlife_pieces = trim($data[5]);
                $ex->case_id = $case->id;
                $ex->reported_by = 1;
                $ex->save();


                $ex = new Exhibit();
                $ex->created_at = Carbon::parse(trim($data[1]));
                $ex->specimen = trim($data[6]);
                $ex->wildlife_quantity = trim($data[16]);
                $wildlife_species = Animal::where('name', trim($data[14]))->first();
                $ex->type_wildlife = "";
                if ($wildlife_species != null) {
                    $ex->wildlife_species = $wildlife_species->id;
                    $ex->type_wildlife = 'Yes';
                }
                $ex->wildlife_pieces = trim($data[17]);
                $ex->wildlife_description = trim($data[18]);

                $implement_name = ImplementType::where('name', trim($data[19]))->first();
                $ex->implement_name = "";
                if ($implement_name != null) {
                    $ex->implement_name = $implement_name->id;
                    $ex->type_implement = 'Yes';
                }
                $ex->implement_pieces = trim($data[20]);
                $ex->implement_description = trim($data[21]);
                $ex->other_implement = trim($data[22]);

                if (
                    $ex->implement_name  > 0 ||
                    $ex->wildlife_species  > 0
                ) {
                    $ex->case_id = $case->id;
                    $ex->reported_by = 1;
                    $ex->save();
                }

                continue;


                $x++;
                if ($x > 100000) {
                    break;
                }
                echo "{$ex->id}";
                continue;

                /*  q
 
  
  23 => "Species Name 3"
  24 => "Specimen 3"
  25 => "Quantity(Kgs) 3"
  26 => "Number of pieces 3"
  27 => "Description 3"
  28 => "Implement Name 3"
  29 => "No of pieces 3"
  30 => "Description 3"
  31 => "Other exhibits description 3"
  32 => "Species Name 4"
  33 => "Specimen 4"
  34 => "Quantity(Kgs) 4"
  35 => "Number of pieces 4"
  36 => "Description 4"
  37 => "Implement Name 4"
  38 => "No of pieces 4"
  39 => "Description 4"
  40 => "Other exhibits description 4"
  41 => "Species Name 5"
  42 => "Specimen 5"
  43 => "Quantity(Kgs) 5"
  44 => "Number of pieces 5"
  45 => "Description 5"
  46 => "Implement Name 5"
  47 => "No of pieces 5"
  48 => "Description 5"
  49 => "Other exhibits description 5"
  50 => "Species Name 6"
  51 => "Specimen 6"
  52 => "Quantity(Kgs) 6"
  53 => "Number of pieces 6"
  54 => "Description 6"
  55 => "Implement Name 6"
  56 => "No of pieces 6"
  57 => "Description 6"
  58 => "Other exhibits description 6"
*/
                $first_name = trim($data[18]);
                $last_name = trim($data[19]);
                $old_suspect = CaseSuspect::where([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'case_id' => $case->id,
                ])->first();

                $isEdit = false;
                if ($old_suspect != null) {
                    $sus = $old_suspect;
                    //echo " Found : {$sus->first_name} {$sus->last_name}";
                    continue;
                } else {
                    $sus = new CaseSuspect();
                }
                echo "<br>$x. ";
                echo " NEW : {$first_name} {$last_name}";


                $ca_text = trim($data[6]);
                $ca = ConservationArea::where('name', $ca_text)->first();
                if ($ca == null) {
                    $ca = ConservationArea::find(1);
                }
                $pa = PA::where('name', trim($data[9]))->first();
                if ($pa == null) {
                    $pa = PA::find(1);
                    //die("No PA found with name " . trim($data[8]));
                }


                $sus->old_supect_number = $old_supect_number;
                $sus->case_id = $case->id;
                $sus->created_at = $case->created_at;
                $sus->pa_id = $pa->id;
                $sus->ca_id = $pa->ca_id;
                $sus->created_by_ca_id = $pa->ca_id;
                //$sus->officer_in_charge = trim($data[7]);
                $sus->arrest_village = trim($data[11]);
                $sus->arrest_village = trim($data[11]);
                $sus->arrest_parish = trim($data[11]);
                $sus->parish = trim($data[11]);
                $sus->village = trim($data[11]);
                $district = Location::where('name', trim($data[12]))->first();
                if ($district != null) {
                    $sus->arrest_district_id = $district->id;
                    $sus->district_id = $district->id;
                }
                $sub_county = Location::where('name', trim($data[13]))->first();
                if ($sub_county != null) {
                    $sus->arrest_sub_county_id = $sub_county->id;
                    $sus->sub_county_id = $sub_county->id;
                }

                $prish_text = trim($data[14]);
                if ($prish_text != null && strlen($prish_text) > 3) {
                    $sus->parish = $prish_text;
                }
                $vil_text = trim($data[15]);
                if ($vil_text != null && strlen($vil_text) > 3) {
                    $sus->village = $vil_text;
                }
                $sus->arrest_detection_method = trim($data[17]);
                $sus->first_name = trim($data[18]);
                $sus->last_name = trim($data[19]);
                $sus->sex = trim($data[20]);
                if ($sus->sex != 'Male' && $sus->sex != 'Female') {
                    $sus->sex = 'Male';
                }
                $sus->age = trim($data[21]);
                $sus->phone_number = trim($data[22]);
                $sus->type_of_id = trim($data[23]);
                $sus->national_id_number = trim($data[24]);
                $sus->occuptaion = trim($data[25]);
                $sus->country = trim($data[26]);
                $district = Location::where('name', trim($data[27]))->first();
                if ($district != null) {
                    $sus->arrest_district_id = $district->id;
                    $sus->district_id = $district->id;
                }


                $sub_county = Location::where('name', trim($data[28]))->first();
                if ($sub_county != null) {
                    $sus->arrest_sub_county_id = $sub_county->id;
                    $sus->sub_county_id = $sub_county->id;
                }

                $vil_text = trim($data[29]);
                if ($vil_text != null && strlen($vil_text) > 3) {
                    $sus->parish = $vil_text;
                }
                $village = trim($data[30]);
                if ($village != null && strlen($vil_text) > 3) {
                    $sus->village = $village;
                }
                $sus->ethnicity = trim($data[31]);

                $offences = [];
                $offences[] = trim($data[32]);
                $offences[] = trim($data[33]);
                $offences[] = trim($data[34]);
                $offences[] = trim($data[35]);
                $at_police = 'No';
                $at_police = trim($data[36]);

                if (strtolower($at_police) == 'yes') {
                    $sus->is_suspects_arrested = 'Yes';
                    $sus->arrest_date_time = Carbon::parse(trim($data[39]));
                    $sus->arrest_in_pa = trim($data[40]);
                    $pa = PA::where('name', trim($data[41]))->first();
                    if ($pa != null) {
                        $sus->arrest_in_pa = $pa->id;
                        $sus->ca_id = $pa->ca_id;
                    } else {
                        $pa = PA::find(1);
                        $sus->ca_id = 1;
                    }
                    $sus->arrest_village = trim($data[43]);

                    $district = Location::where('name', trim($data[44]))->first();
                    if ($district != null) {
                        $sus->arrest_district_id = $district->id;
                    }

                    $sub_county = Location::where('name', trim($data[45]))->first();
                    if ($sub_county != null) {
                        $sus->arrest_sub_county_id = $sub_county->id;
                    }
                    $arrest_village = trim($data[46]);
                    if (
                        $arrest_village != null &&
                        strlen($arrest_village) > 2
                    ) {
                        $sus->arrest_village = $arrest_village;
                        $sus->arrest_parish = $arrest_village;
                    }

                    $arrest_parish = trim($data[47]);
                    if (
                        $arrest_parish != null &&
                        strlen($arrest_parish) > 2
                    ) {
                        $sus->arrest_village = $arrest_parish;
                        $sus->arrest_parish = $arrest_parish;
                    }
                    $sus->arrest_first_police_station = trim($data[50]);
                    $sus->arrest_current_police_station = trim($data[51]);
                    $sus->arrest_agency = trim($data[52]);
                    $sus->arrest_uwa_unit = trim($data[53]);
                    $other_arrest_agencies = [];
                    $other_arrest_agencies_1 = trim($data[54]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        $other_arrest_agencies[] = $other_arrest_agencies_1;
                    }
                    $other_arrest_agencies_1 = trim($data[55]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        if (!in_array($other_arrest_agencies_1, $other_arrest_agencies)) {
                            $other_arrest_agencies[] = $other_arrest_agencies_1;
                        }
                    }
                    $other_arrest_agencies_1 = trim($data[56]);
                    if (strlen($other_arrest_agencies_1) > 2) {
                        if (!in_array($other_arrest_agencies_1, $other_arrest_agencies)) {
                            $other_arrest_agencies[] = $other_arrest_agencies_1;
                        }
                    }
                    $sus->arrest_crb_number = trim($data[57]);
                    $sus->police_sd_number = trim($data[58]);
                    $sus->is_suspect_appear_in_court = trim($data[59]);
                    $sus->status = trim($data[60]);
                    $sus->police_action = trim($data[61]);
                    $sus->police_action_remarks = trim($data[63]);
                    $sus->suspect_appealed_court_file = trim($data[64]);

                    try {
                        $sus->police_action_date = Carbon::parse(trim($data[62]));
                    } catch (\Throwable $th) {
                        $sus->police_action_date = '';
                    }

                    if (strtolower($sus->is_suspect_appear_in_court) == 'yes') {
                        $sus->is_suspect_appear_in_court = 'Yes';
                        $court_date = trim($data[65]);
                        try {
                            $sus->court_date = Carbon::parse(trim($data[62]));
                        } catch (\Throwable $th) {
                            $sus->court_date = $court_date;
                        }

                        $sus->court_name =  trim($data[66]);
                        $sus->prosecutor =  trim($data[67]);
                        $sus->prosecutor =  trim($data[67]);
                        $sus->magistrate_name =  trim($data[68]);
                        $sus->court_status =  trim($data[69]);
                        $sus->suspect_court_outcome =  trim($data[70]);
                        $sus->case_outcome =  trim($data[71]);
                        $sus->case_outcome_remarks =  trim($data[72]);
                        $is_jailed =  trim($data[73]);
                        if (strtolower($is_jailed) == 'yes') {
                            $sus->is_jailed = 'Yes';
                            $jail_date = trim($data[74]);
                            if (strlen($jail_date) > 2) {
                                try {
                                    $sus->jail_date = Carbon::parse($jail_date);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $sus->jail_date = "";
                                }
                            }
                        } else {
                            $sus->is_jailed = 'No';
                        }
                        $sus->jail_period =  trim($data[75]);
                        $sus->prison =  trim($data[76]);
                        try {
                            if (strlen(trim($data[77])) > 3) {
                                $sus->jail_release_date =  Carbon::parse(trim($data[77]));
                            }
                        } catch (\Throwable $th) {
                            $sus->jail_release_date = "";
                        }
                        $sus->is_fined =  trim($data[78]);
                        if ($sus->is_fined != "Yes") {
                            $sus->is_fined = 'No';
                        }
                        $sus->fined_amount =  trim($data[79]);
                        $sus->community_service =  trim($data[80]);
                        $sus->community_service_duration =  trim($data[81]);
                        $sus->cautioned_remarks =  trim($data[82]);
                        $sus->cautioned =  trim($data[82]);
                        $sus->cautioned_remarks =  trim($data[83]);
                        $sus->suspect_appealed =  trim($data[84]);
                        $sus->suspect_appealed_date =  trim($data[85]);
                        if (strlen($sus->suspect_appealed_date) > 3) {
                            try {
                                $sus->suspect_appealed_date = Carbon::parse($sus->suspect_appealed_date);
                            } catch (\Throwable $th) {
                                $sus->suspect_appealed_date = "";
                            }
                        }
                        $sus->suspect_appealed_court_name =  trim($data[86]);
                        $sus->suspect_appealed_court_file =  trim($data[87]);
                        $sus->suspect_appealed_outcome =  trim($data[88]);
                        $sus->suspect_appeal_remarks =  trim($data[89]);
                    } else {
                        $sus->is_suspect_appear_in_court = 'No';
                    }
                } else {
                    $sus->is_suspects_arrested = 'No';
                }
                $sus->management_action = trim($data[37]);
                $sus->not_arrested_remarks = trim($data[38]);

                $sus->reported_by = 1;
                $sus->save();
                foreach ($offences as $_key => $_val) {
                    $offence = Offence::where('name', $_val)->first();
                    if ($offence == null) {
                        continue;
                    }
                    $old = SuspectHasOffence::where([
                        'offence_id' => $offence->id,
                        'case_suspect_id' => $sus->id,
                    ])->first();
                    if ($old == null) {
                        continue;
                    }
                    $hasOffence = new SuspectHasOffence();
                    $hasOffence->case_suspect_id = $sus->id;
                    $hasOffence->offence_id = $offence->id;
                    $hasOffence->save();
                }
                echo (" | success adding $sus->name");
            }
            fclose($handle);
        }

        echo "<pre>";
        print_r($no_names);
        die();
    }


    public static function system_boot($u)
    {
        $u = Admin::user();
        if ($u == null) {
            return;
        }
        if ($u->code != 'Active') {
            $u->code = 'Active';
            $user = User::find($u->id);
            if($user != null){
                $user->code = 'Active';
                $user->save(); 
            }
            //$msg = 'Account not active. Contact admin for help.';
            //die($msg);
            //return;
        }

        $img = '/storage/no_image.png';
        $img_temp = '/storage/no_image_temp.png';
        $base_path = Utils::docs_root();
        $default_img = $base_path . $img;
        $default_img_temp = $base_path . $img_temp;
        //check if file exists
        if (!file_exists($default_img)) {
            //check temp image exists
            if (!file_exists($default_img_temp)) {
                die("Temp image not found.");
            }
            //copy temp image to default image
            copy($default_img_temp, $default_img);
        }
        //check again ig default image exists
        if (!file_exists($default_img)) {
            die("Default image not found.");
        }

        //set unlimited time
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $suspects = CaseSuspect::where(
            'data_copied',
            '!=',
            'Yes'
        )->get();
        /*     $sus = CaseSuspect::find(4831);
        Utils::copyPendingArrestInfo($sus);
        Utils::copyPendingCourtInfo($sus);
        Utils::copyOffencesCourtInfo($sus); */

        foreach ($suspects as $sus) {
            if ($sus->case == null) {
                continue;
            }
            if ($sus->case->case_submitted != 'Yes') {
                //continue;
            }
            Utils::copyPendingArrestInfo($sus);
            Utils::copyPendingCourtInfo($sus);
            Utils::copyOffencesCourtInfo($sus);
            $sus->data_copied = 'Yes';
            $sus->save();
        }


        //self::import_exhibits();
        //dd('import_exhibits');
        //self::import_suspects();
        //die("done importing");
        //die("Imported suspects");
        //self::import_cases();
        //die("Imported cases");


        $sus = CaseSuspect::where('suspect_number', 'like', '%//%')->get();
        foreach ($sus as $key => $sus) {
            $sus->suspect_number = str_replace('//', '/', $sus->suspect_number);
            $sus->save();
        }



        $cases = CaseSuspect::where([
            'reported_by' => null
        ])->get();
        foreach ($cases as $key => $sus) {
            if ($sus->case != null) {
                $sus->reported_by = $sus->case->reported_by;
                try {
                    //code...
                    $sus->save();
                } catch (\Throwable $th) {
                    throw $th;
                    //throw $th;
                }
            }
        }

        foreach (CaseSuspect::where([
            'ca_id' => null
        ])->get() as $key => $sus) {
            if ($sus->case != null) {
                $sus->ca_id = $sus->case->ca_id;
                try {
                    $sus->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
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


        foreach (CaseSuspect::where([
            'suspect_number' => null
        ])->get() as $key => $suspect) {
            //suspect_number
            if ($suspect->case != null) {
                $suspect->is_suspects_arrested = $suspect->case->case_number . "/" . $suspect->id;
                $suspect->save();
            }
        }

        //Fix users with missing ca_id for mobile compatibility
        foreach (User::whereNull('ca_id')->get() as $user) {
            $user->ca_id = $user->pa?->ca?->id;
            $user->save();
        }
    }

    public static function copyOffencesCourtInfo($sus)
    {

        $originalSuspect = CaseSuspect::find($sus->use_offence_suspect_id);
        $sus->use_offence_suspect_coped = 'Yes';
        $sus->save();
        if ($originalSuspect == null) {
            $sus->use_offence = 'No';
            $sus->save();
            return;
        }
        $sus->copyOffencesInfo($originalSuspect);
    }


    public static function copyPendingCourtInfo($sus)
    {
        $suspects = CaseSuspect::where('use_same_court_information_coped', null)->get();

        $originalSuspect = CaseSuspect::find($sus->use_same_court_information_id);
        if ($originalSuspect == null) {
            $sus->use_same_court_information_coped = 'Yes';
            $sus->use_same_court_information = 'No';
            $sus->save();
            return;
        }
        $sus->copyCourtInfo($originalSuspect);
    }

    public static function copyPendingArrestInfo($sus)
    {

        $originalSuspect = CaseSuspect::find($sus->use_same_arrest_information_id);
        if ($originalSuspect == null) {
            $sus->use_same_arrest_information_coped = 'Yes';
            $sus->use_same_arrest_information = 'No';
            $sus->save();
            return;
        }
        $sus->copyArrestInfo($originalSuspect);
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
        if ($ex != null) {
            $pendingCase = CaseModel::find($ex->case_id);
        }
        return $pendingCase;
    }

    //function that checks if is local server
    public static function is_local()
    {
        $server = $_SERVER['SERVER_NAME'];
        if (
            $server == 'localhost' ||
            $server == '127.0.0.1'
        ) {
            return true;
        }
        return false;
    }

    public static function docs_root($params = array())
    {
        if (Utils::is_local()) {
            $path = str_replace('/server.php', "", $_SERVER['SCRIPT_FILENAME']);
            return $path . '/public';
        }
        $r = $_SERVER['DOCUMENT_ROOT'] . "";
        //$r = str_replace('/public', "", $r);
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
        return $c->format('d M, Y');
        return $c->format('d M, Y - h:i a');
    }
    public static function my_date_time_2($t)
    {
        $c = Carbon::parse($t);
        $c->setTimezone(config('app.timezone'));
        if ($t == null) {
            return $t;
        }
        return $c->format('d M, Y - h:i a');
    }

    public static function tell_suspect_status($s)
    {
        if ($s->is_suspect_appear_in_court == null || $s->is_suspect_appear_in_court == 'No') {
            if ($s->is_suspects_arrested == 'Yes') {
                return 'At Police';
            } else {
                return 'Not At Police';
            }
        } else {
            return $s->court_status;
        }
    }

    public static function tell_suspect_status_color($s)
    {
        if ($s->is_suspect_appear_in_court == null || $s->is_suspect_appear_in_court == 'No') {
            if ($s->is_suspects_arrested == 'Yes') {
                return 'warning';
            } else {
                return 'info';
            }
        } else {
            if ($s->court_status == 'Concluded') {
                return 'danger';
            } else if ($s->court_status == 'On-going prosecution') {
                return 'success';
            } else {
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
            "South Sudan",
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

    public static function COUNTRIES_2()
    {
        $data = [];
        foreach ([
            'Uganda',
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
            "South Sudan",
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
