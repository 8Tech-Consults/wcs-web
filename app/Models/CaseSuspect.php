<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseSuspect extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['id',    'created_at',    'updated_at',    'case_id',    'uwa_suspect_number',    'first_name',    'middle_name',    'last_name',    'phone_number',    'national_id_number',    'sex',    'age',    'occuptaion',    'country',    'district_id',    'sub_county_id',    'parish',    'village',    'ethnicity',    'finger_prints',    'is_suspects_arrested',    'arrest_date_time',    'arrest_district_id',    'arrest_sub_county_id',    'arrest_parish',    'arrest_village',    'arrest_latitude',    'arrest_longitude',    'arrest_first_police_station',    'arrest_current_police_station',    'arrest_agency',    'arrest_uwa_unit',    'arrest_detection_method',    'arrest_uwa_number',    'arrest_crb_number',    'is_suspect_appear_in_court',    'prosecutor',    'is_convicted',    'case_outcome',    'magistrate_name',    'court_name',    'court_file_number',    'is_jailed',    'jail_period',    'is_fined',    'fined_amount',    'status'];
    protected $appends = ['photo_url', 'name'];

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($m) { 
            die("Ooops! You cannot delete this item.");
        });
        self::creating(function ($m) {
            $m = CaseSuspect::my_update($m);
            $m->uwa_suspect_number = $m->suspect_number;
            return $m;
        });
        self::updating(function ($m) {
            $m = CaseSuspect::my_update($m);
            $m->uwa_suspect_number = $m->suspect_number;
            return $m;
        });
    }

    public static function my_update($m)
    {
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

        $m->is_suspects_arrested = 0;
        if (
            isset($m->arrest_date_time)
        ) {
            if ($m->arrest_date_time != null) {
                if (strlen(((string)($m->arrest_date_time))) > 5) {
                    $m->is_suspects_arrested = 1;
                }
            }
        }

        $m->is_suspect_appear_in_court = 0;
        if (
            isset($m->use_same_court_information)
        ) {
            if ($m->use_same_court_information != null) {
                if (strlen(((string)($m->use_same_court_information))) > 5) {
                    $m->is_suspect_appear_in_court = 1;
                }
            }
        }


        if (!isset($m->is_convicted)) {
            $m->is_convicted = 0;
        } else if ($m->is_convicted == null) {
            $m->is_convicted = 0;
        }
        return $m;
    }

    function getPhotoUrlAttribute()
    {
        return url('public/storage/images/' . $this->photo);
    }
    function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
    function district()
    {
        return $this->belongsTo(Location::class, 'district_id');
    }
    function sub_county()
    {
        return $this->belongsTo(Location::class, 'sub_county_id');
    }
    public function getNameAttribute()
    {
        return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
    }

    function arrest_district()
    {
        //$ids Location::find($this->arrest_district_id);

        return $this->belongsTo(Location::class, 'arrest_district_id');
    }

    function comments()
    {
        return $this->hasMany(CaseSuspectsComment::class, 'suspect_id');
    }
}
