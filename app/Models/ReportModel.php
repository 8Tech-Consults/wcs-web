<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportModel extends Model
{
    use HasFactory;

    //boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->generated_by_id = auth()->user()->id;
            $model = self::do_prepare($model);
            return $model;
        });
        //updating
        static::updating(function ($model) {
            $model = self::do_prepare($model);
            return $model;
        });
    }

    //do generate function
    public static function do_prepare($m)
    {
        if ($m->is_generated != "No") {
            return;
        }

        if ($m->date_type == 'this_week') {
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
        } else if ($m->date_type == 'previous_week') {
            //seven days before today
            $today = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime($today . ' - 7 days'));
            $end_date = date('Y-m-d', strtotime($today . ' - 1 days'));
        } else if ($m->date_type == 'last_week') {
            $start_date = date('Y-m-d', strtotime('monday last week'));
            $end_date = date('Y-m-d', strtotime('sunday last week'));
        } else if ($m->date_type == 'this_month') {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } else if ($m->date_type == 'previous_month') {
            $start_date = date('Y-m-01', strtotime('first day of last month'));
            $end_date = date('Y-m-t', strtotime('last day of last month'));
        } else if ($m->date_type == 'last_month') {
            $start_date = date('Y-m-01', strtotime('first day of last month - 1 month'));
            $end_date = date('Y-m-t', strtotime('last day of last month - 1 month'));
        } else if ($m->date_type == 'this_year') {
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
        } else if ($m->date_type == 'previous_year') {
            $start_date = date('Y-01-01', strtotime('first day of last year'));
            $end_date = date('Y-12-31', strtotime('last day of last year'));
        } else if ($m->date_type == 'last_year') {
            $start_date = date('Y-01-01', strtotime('first day of last year - 1 year'));
            $end_date = date('Y-12-31', strtotime('last day of last year - 1 year'));
        } else if ($m->date_type == 'custom') {
            $start_date = $m->start_date;
            $end_date = $m->end_date;
        }
        //validate start date and end date
        if ($start_date == null || $end_date == null) {
            throw new \Exception("Invalid date range");
        }
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);

        if ($start_date == null || $end_date == null) {
            throw new \Exception("Invalid date range");
        }

        if ($start_date > $end_date) {
            throw new \Exception("Invalid date range");
        }

        $ca = null;
        $pa = null;
        $type = '';
        $date_generated = date('Y-m-d H:i:s');
        $title = '';
        if ($m->type == 'ca') {
            $ca = ConservationArea::find($m->ca_id);
            if ($ca == null) {
                throw new \Exception("Invalid Conservation Area");
            }
            $title = $ca->name . ' - ' . 'Conservation Area Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'Conservation Area';
        } else if ($m->type == 'pa') {
            $pa = PA::find($m->pa_id);
            if ($pa == null) {
                throw new \Exception("Invalid Protected Area");
            }
            $title = $pa->name . ' - ' . 'Protected Area Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'Protected Area';
        } else if ($m->type == 'all') {
            $title = 'Entire Database - Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'All';
        } else {
            throw new \Exception("Invalid report scope $m->type");
        }
        $m->title = $title;
        return $m;
    }
    public static function doGenerate($m)
    {
        if ($m->is_generated != "No") {
            return;
        }

        if ($m->date_type == 'this_week') {
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
        } else if ($m->date_type == 'previous_week') {
            //seven days before today
            $today = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime($today . ' - 7 days'));
            $end_date = date('Y-m-d', strtotime($today . ' - 1 days'));
        } else if ($m->date_type == 'last_week') {
            $start_date = date('Y-m-d', strtotime('monday last week'));
            $end_date = date('Y-m-d', strtotime('sunday last week'));
        } else if ($m->date_type == 'this_month') {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } else if ($m->date_type == 'previous_month') {
            $start_date = date('Y-m-01', strtotime('first day of last month'));
            $end_date = date('Y-m-t', strtotime('last day of last month'));
        } else if ($m->date_type == 'last_month') {
            $start_date = date('Y-m-01', strtotime('first day of last month - 1 month'));
            $end_date = date('Y-m-t', strtotime('last day of last month - 1 month'));
        } else if ($m->date_type == 'this_year') {
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
        } else if ($m->date_type == 'previous_year') {
            $start_date = date('Y-01-01', strtotime('first day of last year'));
            $end_date = date('Y-12-31', strtotime('last day of last year'));
        } else if ($m->date_type == 'last_year') {
            $start_date = date('Y-01-01', strtotime('first day of last year - 1 year'));
            $end_date = date('Y-12-31', strtotime('last day of last year - 1 year'));
        } else if ($m->date_type == 'custom') {
            $start_date = $m->start_date;
            $end_date = $m->end_date;
        }
        //validate start date and end date
        if ($start_date == null || $end_date == null) {
            throw new \Exception("Invalid date range");
        }
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);

        if ($start_date == null || $end_date == null) {
            throw new \Exception("Invalid date range");
        }

        if ($start_date > $end_date) {
            throw new \Exception("Invalid date range");
        }

        $ca = null;
        $pa = null;
        $type = '';
        $date_generated = date('Y-m-d H:i:s');
        $title = '';
        if ($m->type == 'ca') {
            $ca = ConservationArea::find($m->ca_id);
            if ($ca == null) {
                throw new \Exception("Invalid Conservation Area");
            }
            $title = $ca->name . ' - ' . 'Conservation Area Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'Conservation Area';
        } else if ($m->type == 'pa') {
            $pa = PA::find($m->pa_id);
            if ($pa == null) {
                throw new \Exception("Invalid Protected Area");
            }
            $title = $pa->name . ' - ' . 'Protected Area Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'Protected Area';
        } else if ($m->type == 'all') {
            $title = 'Entire Database - Data Analysis Report for the period of ' . $start_date->format('d M, Y') . ' - ' . $end_date->format('d M, Y') . ' as on ' . date('d M, Y');
            $type = 'All';
        } else {
            throw new \Exception("Invalid report scope $m->type");
        }
        $m->title = $title;
        $m->cases_count = CaseModel::whereBetween('case_date', [$start_date, $end_date])->count();
        $m->suspects_count = CaseSuspect::whereBetween('case_date', [$start_date, $end_date])->count();
        $m->exhibits_count = Exhibit::whereBetween('created_at', [$start_date, $end_date])->count();
        $is_generated = "Yes";

        $generated_by_id = $m->generated_by_id;
        $pdf_file = $m->id . "-" . str_replace(' ', '_', $title) . '.pdf';
        $pdf_file = str_replace('/', '-', $pdf_file);
        $pdf_file = str_replace('\\', '-', $pdf_file);
        $pdf_file = str_replace(':', '-', $pdf_file);
        $update_sql = "UPDATE report_models SET is_generated = '$is_generated', date_generated = '$date_generated', generated_by_id = '$generated_by_id', pdf_file = '$pdf_file' WHERE id = $m->id";
        DB::update($update_sql);
        $m->save();
    }

    public function get_top_conservation_areas()
    {
        $sql = "SELECT ca_id, COUNT(*) as total FROM case_models GROUP BY ca_id ORDER BY total DESC LIMIT 10";
        $query = DB::select($sql);
        $data = [];
        foreach ($query as $key => $value) {
            $ca = ConservationArea::find($value->ca_id);
            if ($ca != null) {
                $data[] = [
                    'name' => $ca->name,
                    'total' => $value->total
                ];
            }
        }
        return $data;
    }

    public function get_top_protected_areas()
    {
        $sql = "SELECT pa_id, COUNT(*) as total FROM case_models GROUP BY pa_id ORDER BY total DESC LIMIT 10";
        $query = DB::select($sql);
        $data = [];
        foreach ($query as $key => $value) {
            $pa = PA::find($value->pa_id);
            if ($pa != null) {
                $data[] = [
                    'name' => $pa->name,
                    'total' => $value->total
                ];
            }
        }
        return $data;
    }

    public function get_top_exhibits()
    {
        $sql = "SELECT wildlife_species, COUNT(*) as total FROM exhibits GROUP BY wildlife_species ORDER BY total DESC LIMIT 10";
        $query = DB::select($sql);
        $data = [];
        foreach ($query as $key => $value) {
            $animal = Animal::find($value->wildlife_species);
            if ($animal != null) {
                $data[] = [
                    'name' => $animal->name,
                    'total' => $value->total
                ];
            }
        }
        return $data;
    }

    /* 
    here is cases data structure


id	
created_at	
updated_at	
reported_by	
latitude	
longitude	
district_id	
sub_county_id	
parish	
village	
offence_category_id	
offence_description	
is_offence_committed_in_pa	
pa_id	
has_exhibits	
status	
title	
location_picker	
deleted_at	
case_number	
done_adding_suspects	
ca_id	
detection_method	
conservation_area_id	
offense_category	
case_submitted	
case_step	
add_more_suspects	
case_date	
officer_in_charge	
court_file_status	
prison	
jail_release_date	
suspect_appealed	
suspect_appealed_date	
suspect_appealed_court_name	
suspect_appealed_court_file	
user_adding_suspect_id	
id_old	
created_by_ca_id	
    */

    /* 
    here is suspects data structure

id	
created_at	
updated_at	
case_id	
uwa_suspect_number	
first_name	
middle_name	
last_name	
phone_number	
national_id_number	
sex	
age	
occuptaion	
country	
district_id	
sub_county_id	
parish	
village	
ethnicity	
finger_prints	
is_suspects_arrested	
arrest_date_time	
arrest_district_id	
arrest_sub_county_id	
arrest_parish	
arrest_village	
arrest_latitude	
arrest_longitude	
arrest_first_police_station	
arrest_current_police_station	
arrest_agency	
other_arrest_agencies	
arrest_uwa_unit	
arrest_detection_method	
arrest_uwa_number	
arrest_crb_number	
is_suspect_appear_in_court	
prosecutor	
is_convicted	
case_outcome	
magistrate_name	
court_name	
court_file_number	
is_jailed	
jail_period	
is_fined	
fined_amount	
status	
deleted_at	
photo	
court_date	
jail_date	
use_same_arrest_information	
use_same_court_information	
suspect_number	
arrest_in_pa	
pa_id	
management_action	
community_service	
reported_by	
add_more_suspects	
ca_id	
not_arrested_remarks	
police_sd_number	
police_action	
police_action_date	
police_action_remarks	
court_file_status	
court_status	
suspect_court_outcome	
is_ugandan	
use_offence	
use_offence_suspect_id	
use_same_arrest_information_id	
use_same_court_information_id	
use_same_arrest_information_coped	
prison	
jail_release_date	
suspect_appealed	
suspect_appealed_date	
suspect_appealed_court_name	
suspect_appealed_court_file	
use_same_court_information_coped	
use_offence_suspect_coped	
type_of_id	
community_service_duration	
suspect_appealed_outcome	
suspect_appeal_remarks	
cautioned	
cautioned_remarks	
other_court_name	
unique_id	
id_old	
case_outcome_remarks	
created_by_ca_id	
old_supect_number	
data_copied	
case_date	

    */


    /*Number of cases associated with PAs= XX (Using the field “Did the case take place in a PA” under Case Information, count all the cases with a “YES” response to this field) */
    public function get_pa_cases()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_offence_committed_in_pa'] = 'Yes';
        return CaseModel::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /*Number of suspects associated with PAs= XX (Using suspects numbers for cases associated with PAs or CAs, e.g. UWA/MFCA/2024/325/1, Count only the suspects with suspect numbers associated with CA or PA) */
    public function get_pa_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'Yes';
        return CaseSuspect::where($conds)
            ->where('pa_id', '>', 1)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of cases not associated with PAs= XX (Using the field “Did the case take place in a PA” under Case Information, count all the cases with a “No” response to this field) */
    public function get_non_pa_cases()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_offence_committed_in_pa'] = 'No';
        return CaseModel::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects not associated with PAs= XX (Using suspects numbers for cases not associated with PAs or CAs, e.g. UWA/NACA/2024/326/2, Count only the suspects with suspect numbers not associated with CA or PA) */
    public function get_non_pa_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        //$conds['is_suspects_arrested'] = 'Yes';
        return CaseSuspect::where($conds)
            ->where('pa_id', 1)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }
    /* Number of male suspects= XX (Using the Sex field under Suspect’s Bio data, count all the individual Male suspects) */
    public function get_male_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['sex'] = 'Male';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of female suspects= XX (Using the Sex field under Suspect’s Bio data, count all the individual Female suspects) */
    public function get_female_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['sex'] = 'Female';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }
    /* Number of suspects below 18 years= XX (Using the field “Suspects Age”, count all the suspects aged 17 years and below (Less than 18 years). DO NOT include those aged 18 years in this count) */
    public function get_under_18_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('age', '<', 18)
            ->count();
    }

    /* Number of suspects above 60 years= XX (Using the field “Suspects Age”, count all the suspects aged 61 years and above (More than 60 years). DO NOT include those aged 60 years in this count) */
    public function get_above_60_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('age', '>', 60)
            ->count();
    }

    /* Number of cases handled at UWA management level only= XX (Using the field “Has the suspect been handled over to Police” under Suspects Bio data, count only cases with a No response to this question)  */
    public function get_uwa_cases()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'No';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects fined by UWA management= XX (Using the field “Action taken by management” under suspect bio data that pops up once a “No” response is given to the field “Has the suspect been handled over to police”, count only the suspects Fined) */
    public function get_fined_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'No';
        $conds['management_action'] = 'Fined';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects cautioned and released by UWA management= XX (Using the field “Action taken by management” under suspect bio data that pops up once a No response is given to the field “Has the suspect been handled over of police”, count only the suspects Cautioned and Released) */
    public function get_cautioned_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'No';
        $conds['management_action'] = 'Cautioned and Released';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects at large= XX (Using the field “Action taken by management” under suspect bio data that pops up once a No response is given to the field “Has the suspect been handled to police”, count only the suspects At Large) */
    public function get_at_large_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'No';
        $conds['management_action'] = 'At Large';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of cases registered at police= XX (Using the field “Has the suspect been handled over to Police” under Suspects Bio data, count only cases with a Yes response to this question) */
    public function get_police_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }

        //with unique case id
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM case_suspects WHERE is_suspects_arrested = 'Yes' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of suspects handled over to police= XX (Using the field “Has the suspect been handled over to Police” under Suspects Bio data, count only cases with a Yes response to this question) */
    public function get_police_suspects()
    {
        $conds = [];
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects in police custody= XX (Using the field “Case status at police level” under arrest information, count the individual suspects under police custody. This field pops up when a No response is given to the field “Has this suspect appeared in court” and either the response “Ongoing investigation” or “Re-opened” are given to field “Case status”)  */
    public function get_police_custody_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'No';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->orwhere('court_status', 'Re-opened')
            ->orwhere('court_status', 'Ongoing investigation')
            ->count();
    }
    /* Number of suspects released on police bond= XX (Using the field “Case status at police level” under arrest information, count the individual suspects on police bond. This field pops up when a No response is given to the field “Has this suspect appeared in court” and either the response “Ongoing investigation” or “Re-opened” are given to field “Case status”) */
    public function get_police_bond_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['police_action'] = 'Police bond';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects that have skipped bond= XX (Using the field “Case status at police level” under arrest information, count the individual suspects that skipped bond. This field pops up when a No response is given to the field “Has this suspect appeared in court” and either the response “Ongoing investigation” or “Re-opened” are given to field “Case status”) */
    public function get_skipped_bond_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['police_action'] = 'Skipped bond';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of suspects that have escaped from police custody= XX (Using the field “Case status at police level” under arrest information, count the individual suspects that have escaped from police custody. This field pops up when a No response is given to the field “Has this suspect appeared in court” and either the response “Ongoing investigation” or “Re-opened” are given to field “Case status”) */
    public function get_escaped_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['police_action'] = 'Escaped from colice custody';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }


    /* Number of cases forwarded to court= XX (Using the field “Has this suspect appeared in court” under court information, count all the cases a YES response to this question)  */
    public function get_forwarded_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        //group by case id
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM case_suspects WHERE is_suspects_arrested = 'Yes' AND is_suspect_appear_in_court = 'Yes' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of suspects forwarded to court= XX (Using the field “Has this suspect appeared in court” under court information, count the individual suspects with a YES response to this question) */
    public function get_forwarded_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of cases undergoing prosecution= XX (Using the field “Court Case Status” under court information, count the cases with “On-going prosecution” as a response to this field)  */
    public function get_ongoing_prosecution_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        $conds['court_status'] = 'On-going prosecution';
        //group by case id
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM case_suspects WHERE is_suspects_arrested = 'Yes' AND is_suspect_appear_in_court = 'Yes' AND court_status = 'On-going prosecution' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    //Number of accused persons undergoing prosecution= XX (Using the field “Court Case Status” under court information, count the individual accused persons with “On-going prosecution” as a response to this field)
    public function get_ongoing_prosecution_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        $conds['court_status'] = 'On-going prosecution';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->count();
    }

    /* Number of accused persons on court bail= XX (Using the field “Accused Court Case Status” under court information, that pops up when the response “Ongoing Prosecution or Reinstated” is provided to the field “Court Case Status”, Count the individual accused persons with Court bail as the option selected) */
    public function get_court_bail_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('suspect_court_outcome', 'Court bail')
            ->count();
    }

    /* Number of accused persons who jumped court bail = XX (Using the field “Accused Court Case Status” under court information, that pops up when the response “Ongoing Prosecution or Reinstated” is provided to the field “Court Case Status”, Count the individual accused persons who “jumped bail and warrant of arrest” as the option selected) */
    public function get_jumped_bail_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        $conds['is_suspects_arrested'] = 'Yes';
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('court_status', 'On-going prosecution')
            ->orwhere('court_status', 'Reinstated')
            ->count();
    }

    /* Number of accused persons on remand= XX (Using the field “Accused Court Case Status” under court information, that pops up when the response “Ongoing Prosecution or Reinstated” is provided to the field “Court Case Status”, Count the individual accused persons with Remand as the option selected) */
    public function get_remand_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        /* 
          "Remand" => "Remand"
    "Court bail" => "Court bail"
    "Hearing" => "Hearing"
    "Committal" => "Committal"
    "Ongoing inquiries" => "Ongoing inquiries"
    "Remand and hearing" => "Remand and hearing"
    "Remand and ongoing inquiries" => "Remand and ongoing inquiries"
    "Court bail and hearing" => "Court bail and hearing"
    "Court bail and ongoing inquiries" => "Court bail and ongoing inquiries"
    "Jumped bail and warrant of arrest" => "Jumped bail and warrant of arrest"
        */
        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('suspect_court_outcome', 'Jumped bail and warrant of arrest')
            ->count();
    }

    /* Number of accused persons on remand= XX (Using the field “Accused Court Case Status” under court information, that pops up when the response “Ongoing Prosecution or Reinstated” is provided to the field “Court Case Status”, Count the individual accused persons with Remand as the option selected)  */
    public function get_hearing_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }

        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('suspect_court_outcome', 'Hearing')
            ->count();
    }

    /* Number of concluded cases= XX (Using the field “Court Case Status” under court information, count the cases with “Concluded” as a response to this field) */
    public function get_concluded_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }

        $conds['is_suspects_arrested'] = 'Yes';
        $conds['is_suspect_appear_in_court'] = 'Yes';
        $conds['court_status'] = 'Concluded';
        //group by case id
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM case_suspects WHERE is_suspects_arrested = 'Yes' AND is_suspect_appear_in_court = 'Yes' AND court_status = 'Concluded' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of dismissed cases= XX ((Using the field “Specific court Case Status” under court information, count the Cases with “Dismissed” as a response to this field) */
    public function get_dismissed_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        //group by case id
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM case_suspects WHERE  case_outcome = 'Dismissed' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of accused persons convicted= XX (Using the field “Specific court Case Status” under court information, count the individual accused persons with “Convicted” as a response to this field) */
    public function get_convicted_suspects()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('case_outcome', 'Convicted')
            ->count();
    }

    /* Number of convicts jailed= XX (Using the field “Was accused jailed” under court information, count individual accused persons with a YES to this question) */
    public function get_jailed_convicts()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('is_jailed', 'Yes')
            ->count();
    }

    /* Number of convicts fined= XX (Using the field “Was accused fined” under court information, count individual accused persons with a YES to this question) */
    public function get_fined_convicts()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('is_fined', 'Yes')
            ->count();
    }

    /* Number of convicts that were given a community service= XX (Using the field “Was the accused offered community service” under court information, count individual accused persons with a YES to this question) */
    public function get_community_service_convicts()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('community_service', 'Yes')
            ->count();
    }

    /* Number of accused persons that were cautioned= XX (Using the field “Was accused cautioned” under court information, count individual accused persons with a YES to this question) */
    public function get_cautioned_convicts()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('cautioned', 'Yes')
            ->count();
    }

    /* Number of accused persons that were Acquitted= XX (Using the field “Specific court Case Status” under court information, count the individual accused persons with “Acquittal” as a response to this field) */
    public function get_acquitted_convicts()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }


        return CaseSuspect::where($conds)
            ->whereBetween('case_date', [$this->start_date, $this->end_date])
            ->where('case_outcome', 'Acquittal')
            ->count();
    }

    /* Number of cases involving Elephants= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Elephant as a species name) */
    public function get_elephant_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }

        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        return Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('wildlife_species', 2)
            ->count();
    }

    /* Quantity in Kgs of Elephant Ivory = XX (Using the field “Quantity (in Kgs)” under Wildlife Exhibit Information, sum up the Kgs of Ivory as a specimen name selected under the field “Specimen” for only Elephant species selected under the field “Species name”) */
    public function get_elephant_ivory_kgs()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('specimen', 'Ivory')
            ->where('wildlife_species', 2)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $total += (int)$exhibit->wildlife_quantity;
        }
        return $total;
    }

    /* 
    1. Other
2. Elephant
3. Lion
4. Aardvark
5. Leopard
6. Rhino
7. Wild Dog
8. Red tailed monkey
9. Blue monkey
10. Uganda Kob
11. Giraffe
12. Shoebill Stork
13. Grey Parrot
14. Skimmer
15. Plants
16. Martial Eagle
17. Grey-crowned Crane
18. Forest Hog
19. Crocodile
20. Clawless Otter
21. Sitatunga
22. Side-striped Jackal
23. Patas Monkey
24. Civet
25. Serval Cat
26. Spotted-necked Otter
27. Goliath Heron
28. Papyrus Gonolek
29. Papyrus Yellow Warbler
30. Papyrus Canary
31. Shoebill
32. Other
33. Elephant
34. Lion
35. Buffalo
36. Leopard
37. Rhino
38. Wild Dog
39. Chimpanzee
40. Gorilla
41. Uganda Kob
42. Giraffe
43. Shoebill Stork
44. Grey Parrot
45. Skimmer
46. Nubian Vulture
47. Martial Eagle
48. Grey-crowned Crane
49. Forest Hog
50. unknown
51. Clawless Otter
52. African fish Eagle
53. Side-striped Jackal
54. Patas Monkey
55. Civet
56. Serval Cat
57. Spotted-necked Otter
58. Goliath Heron
59. Papyrus Gonolek
60. Papyrus Yellow Warbler
61. Papyrus Canary
62. Shoebill
63. Bushbuck
64. Pangolin
65. Hippopotamus
66. Bush pig
67. Impala
68. Bamboo
69. Oribi
70. Duiker
71. Jacksons Heartbeast
72. Ostrich
73. Reedbuck
74. Waterbuck
75. Warthog
76. Eland
77. Zebra
78. Gazelle
79. Otta
80. Olive Baboon
81. L'Hosts monkey
82. Grey Cheeked mangabey
83. Red tailed monkey
84. Red tailed monkey
85. Black & white colobus monkey
86. Cheetah
87. Rock hyrax
88. Tortoise
89. Love birds
90. African green pigeons
91. Francolin
92. Cormorant
93. Python
94. Hedge Hog
95. Giant Forest Hog
96. Dik dik
97. Hadada ibis
98. Monitor lizard
99. Lesser kudu
100. Black Flying Fox
101. Genet cat
102. Guinea fowls
    */

    /* Number of cases involving Pangolins= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the cases with Pangolin as a species name) */
    public function get_pangolin_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 64 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* 
    specimen

      "Skins" => "Skins"
    "Meat" => "Meat"
    "Live animal" => "Live animal"
    "Eggs" => "Eggs"
    "Molars" => "Molars"
    "Jaws" => "Jaws"
    "Spikes /Ruills" => "Spikes /Ruills"
    "Hair" => "Hair"
    "Bone" => "Bone"
    "Bangle" => "Bangle"
    "Chopsticks" => "Chopsticks"
    "Rosary" => "Rosary"
    "Necklace" => "Necklace"
    "Belt" => "Belt"
    "Handbag" => "Handbag"
    "Timber" => "Timber"
    "Sculpture" => "Sculpture"
    "Beads" => "Beads"
    "Carcass" => "Carcass"
    "Ivory" => "Ivory"
    "Rhino horn" => "Rhino horn"
    "Hippo Teeth" => "Hippo Teeth"
    "Pangolin Scales" => "Pangolin Scales"
    "Lion oil" => "Lion oil"
    "Feathers" => "Feathers"
    "Legs" => "Legs"
    "Firewood" => "Firewood"
    "Charcoal" => "Charcoal"
    "Tail" => "Tail"
    "Head" => "Head"
    "Hooves" => "Hooves"
    "Skull" => "Skull"
    "Pepper genesis seeds" => "Pepper genesis seeds"
    "Mingling sticks" => "Mingling sticks"
    "Prunus africana bark" => "Prunus africana bark"
    "Walking sticks" => "Walking sticks"
    "Poles" => "Poles"
    "Logs" => "Logs"
    "Tortoise shells" => "Tortoise shells"
    "Hoe handles" => "Hoe handles"
    "Horns" => "Horns"
    "Bamboo shoots" => "Bamboo shoots"
    "Lion teeth" => "Lion teeth"
    "Bamboo reeds" => "Bamboo reeds"
    "Warthog teeth" => "Warthog teeth"
    "Leopard teeth" => "Leopard teeth"
    "Crocodile teeth" => "Crocodile teeth"
    "Bushpig teeth" => "Bushpig teeth"
    "Elephant Molars" => "Elephant Molars"
    "Egg shells" => "Egg shells"
    "Marble stones" => "Marble stones"
    */

    /* Number of live pangolins= XX (Using the field “Specimen” under Wildlife Exhibit Information, count the live animals that belong to Pangolin as a species name selected under the field “Select species”) */
    public function get_live_pangolins()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }

        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('specimen', 'Live animal')
            ->where('wildlife_species', 64)
            ->get();
        $number = 0;
        foreach ($exhibits as $exhibit) {
            $x = (int)$exhibit->wildlife_quantity;
            if ($x < 1) {
                $x = 1;
            }
            $number += $x;
        }
        return $number;
    }

    /* Quantity in Kgs of Pangolin scales= XX (Using the field “Quantity (in Kgs)” under Wildlife Exhibit Information, sum up the Kgs of Pangolin scales as a specimen name selected under the field “Specimen” for only Pangolin species selected under the field “Species name”) */
    public function get_pangolin_scales_kgs()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('specimen', 'Pangolin Scales')
            ->where('wildlife_species', 64)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $total += (int)$exhibit->wildlife_quantity;
        }
        return $total;
    }

    /* Number of cases involving Hippopotamus= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the cases with Hippopotamus as species name) */
    public function get_hippopotamus_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 65 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Quantity in Kgs of Hippo Teeth= XX (Using the field “Quantity (in Kgs)” under Wildlife Exhibit Information, sum up the Kgs of Hippo Teeth as a specimen name selected under the field “Specimen” for only Hippopotamus species selected under the field “Species name”) */
    public function get_hippo_teeth_kgs()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('specimen', 'Hippo Teeth')
            ->where('wildlife_species', 65)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $total += (int)$exhibit->wildlife_quantity;
        }
        return $total;
    }

    /* Number of cases involving bushmeat= XX (Using the field “Specimen” under Wildlife Exhibit Information, count all the cases with Meat as a specimen name)  */
    public function get_bushmeat_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE specimen = 'Meat' AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Quantity in Kgs of bushmeat= XX (Using the field “Quantity (in Kgs)” under Wildlife Exhibit Information, sum up the Kgs of Meat as a specimen name for all species selected under the field “Specimen”) */
    public function get_bushmeat_kgs()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('specimen', 'Meat')
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $total += (int)$exhibit->wildlife_quantity;
        }
        return $total;
    }


    /* Number of cases involving Lions= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases of with Lion as a species name) */
    public function get_lion_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 3 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Leopards= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Leopard as species name) */
    public function get_leopard_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 5 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Gorillas= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Gorilla as a species name) */
    public function get_gorilla_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 40 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Chimpanzees= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Chimpanzee as a species name) */
    public function get_chimpanzee_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 39 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Giraffes= XX (Using the field “Select species” under Exhibits that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Giraffe as the species name) */
    public function get_giraffe_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 42 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Uganda Kobs= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Uganda Kob as a species name) */
    public function get_uganda_kob_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 41 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Buffalos= XX (Using the field “Select species” under Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Buffalo as a species name) */
    public function get_buffalo_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 35 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of cases involving Rhinos= XX (Using the field “Select species” Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Rhino as a species name) */
    public function get_rhino_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 6 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }
    /* Number of cases involving Parrots= XX (Using the field “Select species” Wildlife Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Wildlife”, count the Cases with Parrot as species name) */
    public function get_parrot_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where species is pangolin
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE wildlife_species = 13 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }
    /* 
Implements id and names list, id saved in implement_name in exhibits
   28 => "Catapult"
    27 => "Hand saw"
    26 => "Fishing hooks"
    25 => "Power saw"
    24 => "Cartridges"
    23 => "Ammunition"
    22 => "Hunting nets"
    21 => "Oars"
    20 => "Fishing net"
    19 => "Canoe"
    18 => "Wheel traps"
    17 => "Metal traps"
    16 => "Saucepans"
    15 => "Hoe"
    14 => "Pit saw"
    13 => "Sharpening file"
    12 => "Axe"
    11 => "Arrows"
    10 => "Bows"
    9 => "Fishing baskets"
    8 => "Spade"
    7 => "Hookline"
    6 => "Spears"
    5 => "Wire snares"
    4 => "Guns"
    3 => "Knives"
    2 => "Pangas"
*/
    /* Number of cases involving wire snares= XX (Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the cases with Wire Snare as an implement type) */
    public function get_wire_snare_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 5 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 5 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }
    /* Number of wire snares= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of wire snares selected under the field “Select Implement”) */
    public function get_wire_snare_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 5)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving guns= XX (Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the cases with Gun as an implement type) */
    public function get_gun_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 4 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 4 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of guns= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Guns selected under the field “Select Implement”) */
    public function get_gun_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 4)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving ammunition= XX (Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the cases with Ammunition as an implement type) */
    public function get_ammunition_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 23 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 23 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of ammunitions= XX ((Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Ammunition selected under the field “Select Implement”) */
    public function get_ammunition_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 23)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }


    /* Number of cases involving spears= XX Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the Cases with Spear as an implement type) */
    public function get_spear_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 6 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 6 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of spears= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Spears selected under the field “Select Implement”) */
    public function get_spear_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 6)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving pangas= XX (Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the number of Panga implements) */
    public function get_panga_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 2 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 2 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of pangas= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Pangas selected under the field “Select Implement”) */
    public function get_panga_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 2)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving arrows= XX Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the Cases with Arrow as an implement type) */
    public function get_arrow_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 11 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 11 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of arrows= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Arrows selected under the field “Select Implement”) */
    public function get_arrow_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 11)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving metal traps= XX (Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the Cases with Metal trap as an implement type) */
    public function get_metal_trap_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 17 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 17 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of metal traps= XX (Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Metal traps selected under the field “Select Implement”) */
    public function get_metal_trap_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 17)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }

    /* Number of cases involving knives= XX Using the field “Select Implement” under Implement Exhibit Information, that pops up when a YES response is provided to the field “Exhibit type Implement”, count the Cases with Knife as an implement type) */
    public function get_knife_cases()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        //count group by case id where implement_name is 3 in exhibits table
        $sql = "SELECT COUNT(DISTINCT case_id) as total FROM exhibits WHERE implement_name = 3 AND case_id IN (SELECT id FROM case_models WHERE 1 $where AND case_date BETWEEN '$this->start_date' AND '$this->end_date')";
        $query = DB::select($sql);
        return $query[0]->total;
    }

    /* Number of knives= XX ((Using the field “No. of pieces” under Implement Exhibit Information, sum up the pieces of Knives selected under the field “Select Implement”) */
    public function get_knife_pieces()
    {
        $conds = [];
        $where = '';
        if ($this->type == 'pa') {
            $conds = ['pa_id' => $this->pa_id];
            $where = " AND pa_id = $this->pa_id";
        } else if ($this->type == 'ca') {
            $conds = ['ca_id' => $this->ca_id];
            $where = " AND ca_id = $this->ca_id";
        } else if ($this->type == 'all') {
            $conds = [];
        }
        if (isset($conds['pa_id'])) unset($conds['pa_id']);
        $exhibits = Exhibit::where($conds)
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->where('implement_name', 3)
            ->get();
        $total = 0;
        foreach ($exhibits as $exhibit) {
            $num = (int)$exhibit->implement_pieces;
            if ($num < 1) {
                $num = 1;
            }
            $total += $num;
        }
        return $total;
    }
}
