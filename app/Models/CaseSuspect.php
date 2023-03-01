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


    public static function boot()
    {
        parent::boot();
        self::deleting(function ($m) {
        });

        self::creating(function ($m) {
            $m->case->case_step = 1;
            if ($m->add_more_suspects == 'No') {
                if ($m->case != null) {
                    $m->case->case_step = 2;
                    $m->case->save();
                }
            }
        });
        self::creating(function ($m) {
            $case = CaseModel::find($m->case_id);

            if ($case != null) {
                $m->suspect_number = $case->get_suspect_number();
            }
            $m = CaseSuspect::my_update($m);
            $m->uwa_suspect_number = $m->suspect_number;
            $m->arrest_uwa_number = $m->suspect_number;

            return $m;
        });
        self::updating(function ($m) {
            $m = CaseSuspect::my_update($m);
            $m->uwa_suspect_number = $m->suspect_number;
            $m->arrest_uwa_number = $m->suspect_number;
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


        if (
            isset($m->arrest_date_time)
        ) {
            if ($m->arrest_date_time != null) {
                if (strlen(((string)($m->arrest_date_time))) > 5) {
                    $m->is_suspects_arrested = 1;
                }
            }
        }


        if (
            isset($m->use_same_court_information)
        ) {
            if ($m->use_same_court_information != null) {
                if (strlen(((string)($m->use_same_court_information))) > 5) {
                    $m->is_suspect_appear_in_court = 1;
                }
            }
        }



        return $m;
    }

    function getPhotoUrlAttribute()
    {
        return url('public/storage/images/' . $this->photo);
    }
    function offences()
    {
        return $this->belongsToMany(Offence::class, 'suspect_has_offences');
        return $this->hasMany(SuspectHasOffence::class, 'suspect_id');
    }


    function vaditcs()
    {
        return $this->hasMany(SuspectHasOffence::class, 'case_suspect_id');
    }

    function ca()
    {
        $ca =  ConservationArea::find($this->ca_id);
        if ($ca == null) {
            $this->ca_id = 1;
            $this->save();
        }
        return $this->belongsTo(ConservationArea::class, 'ca_id');
    }


    function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }


    function copyOffencesInfo($org)
    {


        foreach ($org->offences as $key => $of) {
            $newOff = new SuspectHasOffence();
            $newOff->case_suspect_id = $this->id;
            $newOff->offence_id = $of->id;
            $newOff->vadict = null;
            $newOff->save();
            //dd($of);
        }
        $this->use_offence_suspect_coped = 'Yes';
        $this->use_offence = 'Yes';
        $this->save();
    }
    function copyCourtInfo($org)
    {

        $this->is_suspect_appear_in_court = $org->is_suspect_appear_in_court;
        $this->prosecutor = $org->prosecutor;
        $this->is_convicted = $org->is_convicted;
        $this->case_outcome = $org->case_outcome;
        $this->magistrate_name = $org->magistrate_name;
        $this->court_name = $org->court_name;
        $this->court_file_number = $org->court_file_number;
        $this->is_jailed = $org->is_jailed;
        $this->jail_period = $org->jail_period;
        $this->is_fined = $org->is_fined;
        $this->fined_amount = $org->fined_amount;
        $this->court_date = $org->court_date;
        $this->jail_date = $org->jail_date;
        $this->court_file_status = $org->court_file_status;
        $this->court_status = $org->court_status;
        $this->suspect_court_outcome = $org->suspect_court_outcome;
        $this->use_same_court_information_id = $org->use_same_court_information_id;
        $this->use_same_court_information_coped = $org->use_same_court_information_coped;
        $this->use_same_court_information_coped = 'Yes';
        $this->use_same_court_information = 'Yes';
        $this->save();
    }



    function copyArrestInfo($org)
    {
        $this->is_suspects_arrested = $org->is_suspects_arrested;
        $this->arrest_date_time = $org->arrest_date_time;
        $this->arrest_district_id = $org->arrest_district_id;
        $this->arrest_sub_county_id = $org->arrest_sub_county_id;
        $this->arrest_parish = $org->arrest_parish;
        $this->arrest_village = $org->arrest_village;
        $this->arrest_latitude = $org->arrest_latitude;
        $this->arrest_longitude = $org->arrest_longitude;
        $this->arrest_first_police_station = $org->arrest_first_police_station;
        $this->arrest_current_police_station = $org->arrest_current_police_station;
        $this->arrest_agency = $org->arrest_agency;
        $this->arrest_uwa_unit = $org->arrest_uwa_unit;
        $this->arrest_detection_method = $org->arrest_detection_method;
        $this->arrest_uwa_number = $org->arrest_uwa_number;
        $this->arrest_crb_number = $org->arrest_crb_number;
        $this->arrest_in_pa = $org->arrest_in_pa;
        $this->pa_id = $org->pa_id;
        $this->management_action = $org->management_action;
        $this->community_service = $org->community_service;
        $this->ca_id = $org->ca_id;
        $this->not_arrested_remarks = $org->not_arrested_remarks;
        $this->police_sd_number = $org->police_sd_number;
        $this->police_action = $org->police_action;
        $this->police_action_date = $org->police_action_date;
        $this->police_action_remarks = $org->police_action_remarks;
        $this->court_file_status = $org->court_file_status;
        $this->court_status = $org->court_status;
        $this->use_same_arrest_information_coped = 'Yes';
        $this->use_same_arrest_information = 'Yes';
        $this->save();
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

    function getArrestSubCountyTextAttribute()
    {
        $d = Location::find($this->arrest_sub_county_id);
        if ($d == null) {
            return '-';
        }
        return $d->name_text;
    }
    function getArrestDistrictTextAttribute()
    {
        $d = Location::find($this->arrest_district_id);
        if ($d == null) {
            return '-';
        }
        return $d->name;
    }

    protected $appends = [
        'photo_url',
        'name',
        'arrest_sub_county_text',
        'arrest_district_text',
    ];
}
