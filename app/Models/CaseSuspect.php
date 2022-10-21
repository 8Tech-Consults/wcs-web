<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseSuspect extends Model
{
    use HasFactory; 

    protected $fillable = ['id',	'created_at',	'updated_at',	'case_id',	'uwa_suspect_number',	'first_name',	'middle_name',	'last_name',	'phone_number',	'national_id_number',	'sex',	'age',	'occuptaion',	'country',	'district_id',	'sub_county_id',	'parish',	'village',	'ethnicity',	'finger_prints',	'is_suspects_arrested',	'arrest_date_time',	'arrest_district_id',	'arrest_sub_county_id',	'arrest_parish',	'arrest_village',	'arrest_latitude',	'arrest_longitude',	'arrest_first_police_station',	'arrest_current_police_station',	'arrest_agency',	'arrest_uwa_unit',	'arrest_detection_method',	'arrest_uwa_number',	'arrest_crb_number',	'is_suspect_appear_in_court',	'prosecutor',	'is_convicted',	'case_outcome',	'magistrate_name',	'court_name',	'court_file_number',	'is_jailed',	'jail_period',	'is_fined',	'fined_amount',	'status'];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($m) {
            $m->district_id = 1;
            if ($m->sub_county_id != null) {
                $sub = Location::find($m->sub_county_id);
                if ($sub != null) {
                    $m->district_id = $sub->parent;
                }
            }
            
            if ($m->arrest_sub_county_id != null) {
                $sub = Location::find($m->arrest_sub_county_id);
                if ($sub != null) {
                    $m->arrest_district_id = $sub->parent;
                }
            }

            return $m;
        });
        
    }

    function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
}
