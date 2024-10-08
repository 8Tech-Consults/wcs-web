<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaseSuspect extends Model
{
    use HasFactory;

    protected $fillable = ['id',    'created_at',    'updated_at',    'case_id',    'uwa_suspect_number',    'first_name',    'middle_name',    'last_name',    'phone_number',    'national_id_number',    'sex',    'age',    'occuptaion',    'country',    'district_id',    'sub_county_id',    'parish',    'village',    'ethnicity',    'finger_prints',    'is_suspects_arrested',    'arrest_date_time',    'arrest_district_id',    'arrest_sub_county_id',    'arrest_parish',    'arrest_village',    'arrest_latitude',    'arrest_longitude',    'arrest_first_police_station',    'arrest_current_police_station',    'arrest_agency',  'arrest_uwa_unit',    'arrest_detection_method',    'arrest_uwa_number',    'arrest_crb_number',    'is_suspect_appear_in_court',    'prosecutor',  'court_status',  'is_convicted',    'case_outcome',    'magistrate_name',    'court_name',    'court_file_number',    'is_jailed',    'jail_period',    'is_fined',    'fined_amount',    'status'];


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
            if ($m->is_suspects_arrested == null || $m->is_suspects_arrested == "") {
                $m->is_suspects_arrested = 'No';
            }

            $m = CaseSuspect::my_update($m);

            $by = User::find($m->reported_by);
            if ($by == null) {
                $by = Auth::user();
                if ($by == null) {
                    throw new \Exception("Created by is not a user.");
                }
            }

            if ($m->created_by_ca_id == null || $m->created_by_ca_id == 0 || strlen($m->created_by_ca_id) < 1) {
                $m->created_by_ca_id = $by->ca_id;
            }

            $m->case_date =  $m->case->case_date;
            return $m;
        });
        self::created(function ($m) {
            $case = CaseModel::find($m->case_id);
            if ($case != null) {
                $case->user_adding_suspect_id = null;
                $case->save();
            }
        });
        self::updated(function ($m) {
            $case = CaseModel::find($m->case_id);
            if ($case != null) {
                $case->user_adding_suspect_id = null;
                $case->save();
            }
        });
        self::updating(function ($m) {

            if ($m->is_suspects_arrested == null || $m->is_suspects_arrested == "") {
                $m->is_suspects_arrested = 'No';
            }

            $m = CaseSuspect::my_update($m, true);
            if ($m->case_submitted == 1 || $m->case_submitted == '1') {
                $m->case_submitted = '1';
            }
            $m->case_date =  $m->case->case_date;
            return $m;
        });
    }

    public function otherCasese()
    {
        $instance_1 = SuspectLink::where(['suspect_id_1' => $this->id])->get();
        $instance_2 = SuspectLink::where(['suspect_id_2' => $this->id])->get();
        $ids = [];
        foreach ($instance_1 as $key => $value) {
            $ids[] = $value->suspect_id_2;
        }
        foreach ($instance_2 as $key => $value) {
            $ids[] = $value->suspect_id_1;
        }
        return CaseSuspect::whereIn('id', $ids)->get();
    }
    public static function my_update($m, $updating = false)
    {
        $m->district_id = 0;

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
        if (!$updating) {
            $case = CaseModel::find($m->case_id);
            if ($case != null) {
                $m->suspect_number = $case->get_suspect_number($m);
            } else {
                throw new Exception("Suspect case not found.", 1);
            }

            $m->uwa_suspect_number = $m->suspect_number;
            $m->arrest_uwa_number = $m->suspect_number;
        }

        //Reset the following fields if suspect appeared in court
        if ($m->is_suspect_appear_in_court == 'Yes') {
            $m->police_action = null;
            $m->police_action_date = null;
            $m->police_action_remarks = null;
            $m->status = null;
        }

        if (
            $m->is_suspects_arrested == 1 ||
            $m->is_suspects_arrested == 'Yes' ||
            $m->is_suspects_arrested == 'yes'
        ) {
            $m->is_suspects_arrested = 'Yes';
        } else {
            $m->is_suspect_appear_in_court = 'No';  //TODO: look into what this does
            $m->is_suspects_arrested == 'No';
        }


        if ($m->is_suspects_arrested == 'Yes') {
            $pa = PA::find($m->pa_id);
            if ($pa != null) {
                $m->arrest_in_pa = 'Yes';
                $m->ca_id = $pa->ca_id;
                $sub = Location::find($pa->subcounty);
                if ($sub != null) {
                    $pa->arrest_sub_county_id = $sub->id;
                    $pa->arrest_district_id = $sub->parent;
                }
            } else {
                $m->pa_id = 1;
                $m->ca_id = 1;
                $m->arrest_in_pa = 'No';
            }
        }

        if ($m->pa_id == 1) {
            $m->arrest_in_pa = 'No';
        }

        return $m;
    }

    public function getOtherArrestAgenciesAttribute($value)
    {
        if ($value == null || $value == "") {
            return [];
        }
        $value = json_decode($value);
        // if the value is a string then convert it to an array
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        return $value;
    }

    public function setOtherArrestAgenciesAttribute($value)
    {
        $this->attributes['other_arrest_agencies'] = json_encode($value);
    }

    function getPhotoUrlAttribute()
    {
        if ($this->photo == '' || $this->photo == null) return url('public/storage/no_image.png');
        return url('public/storage/' . $this->photo);
    }


    function getStatusAttribute($s)
    {

        if ($s == '1') {
            return 'On-going investigation';
        } else if ($s == '2') {
            return 'Closed';
        } else if ($s == '3') {
            return 'Re-opened';
        } else {
            return $s;
        }
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
        $case = CaseModel::find($this->case_id);
        if ($case == null) {
            $this->delete();
        }
        return $this->belongsTo(CaseModel::class, 'case_id');
    }


    function copyOffencesInfo($org)
    {

        $offences = $org->offences;

        if ($offences->count() < 1) {
            if ($org->case != null) {
                $offences = $org->case->getAllOffences();
            }
        }

        foreach ($offences as $key => $of) {
            $old = SuspectHasOffence::where([
                'case_suspect_id' => $this->id,
                'offence_id' => $of->id,
            ])->first();
            if ($old != null) {
                continue;
            }
            $newOff = new SuspectHasOffence();
            $newOff->case_suspect_id = $this->id;
            $newOff->offence_id = $of->id;
            $newOff->vadict = null;
            $newOff->save();
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
        $this->cautioned_remarks = $org->cautioned_remarks;
        $this->cautioned = $org->cautioned;
        $this->suspect_court_outcome = $org->suspect_court_outcome;
        $this->use_same_court_information_id = $org->use_same_court_information_id;
        $this->use_same_court_information_coped = $org->use_same_court_information_coped;
        $this->community_service_duration = $org->community_service_duration;
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
        $this->other_arrest_agencies = $org->other_arrest_agencies;
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
        $this->community_service_duration = $org->community_service_duration;
        $this->use_same_arrest_information_coped = 'Yes';
        $this->use_same_arrest_information = 'Yes';
        $this->save();
    }


    function district()
    {
        $sub = Location::find($this->district_id);
        if ($sub == null) {
            $this->district_id = 0;
            $this->save();
        }
        return $this->belongsTo(Location::class, 'district_id');
    }
    function court()
    {
        return $this->belongsTo(Court::class, 'court_name');
    }
    function arrestPa()
    {
        $ap = PA::find($this->pa_id);
        if ($ap == null) {
            $this->pa_id = 1;
            $this->arrest_in_pa = 'No';
            $this->save();
        }
        return $this->belongsTo(PA::class, 'pa_id');
    }
    function arrestCa()
    {
        return $this->belongsTo(ConservationArea::class, 'ca_id');
    }
    function sub_county()
    {
        $sub = Location::find($this->sub_county_id);
        if ($sub == null) {
            $this->sub_county_id = 0;
            $this->save();
        }
        return $this->belongsTo(Location::class, 'sub_county_id');
    }
    public function getNameAttribute()
    {
        return $this->first_name . " " . $this->middle_name . " " . $this->last_name;
    }

    function arrest_district()
    {
        //$ids Location::find($this->arrest_district_id);

        $sub = Location::find($this->arrest_district_id);
        if ($sub == null) {
            $this->arrest_district_id = 0;
            $this->save();
        }

        return $this->belongsTo(Location::class, 'arrest_district_id');
    }

    function comments()
    {
        return $this->hasMany(CaseSuspectsComment::class, 'suspect_id');
    }

    function getOffencesTextAttribute()
    {
        if ($this->offences == null) {
            return "-";
        }
        $txt = "";
        $x = 0;
        $ids = [];
        foreach ($this->offences as $key => $value) {
            $x++;

            if (in_array($value->id, $ids)) {
                continue;
            }
            $ids[] = $value->id;
            $txt .= $value->name;
            if ($x != $this->offences->count()) {
                $txt .= ', ';
            } else {
                $txt .=  '.';
            }
        }
        return $txt;
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
        'offences_text',
        'arrest_district_text',
    ];
}
