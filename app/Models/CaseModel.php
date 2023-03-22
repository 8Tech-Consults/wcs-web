<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;


    public static function boot()
    {
        parent::boot();
        self::deleting(function ($m) {

            if ($m->id == 1) {
                die("Ooops! You cannot delete this item.");
            }

            CaseSuspect::where([
                'case_id' => $m->id
            ])->delete();
            CaseComment::where([
                'case_id' => $m->id
            ])->delete();
            Exhibit::where([
                'case_id' => $m->id
            ])->delete();
        });
        self::created(function ($m) {
            $m->case_number = Utils::getCaseNumber($m);
            $m->save();
            try {
                CaseModel::created_suspectes($m);
            } catch (\Throwable $th) {
                //throw $th;
            }
        });
        self::creating(function ($m) {

            $m->district_id = 1;
            $m->has_exhibits = 0;

            if ($m->sub_county_id != null) {
                $sub = Location::find($m->sub_county_id);
                if ($sub != null) {
                    $m->district_id = $sub->parent;
                }
            }
            $m->offence_description = $m->title;
            $m->case_step = 1;

            if ($m->is_offence_committed_in_pa == 'No') {
                $m->ca_id = 1;
                $m->pa_id = 1;
            }
            $m->case_number = Utils::getCaseNumber($m);  
            return $m;
        });
        self::updating(function ($m) {
 
            $m->case_number = Utils::getCaseNumber($m); 
            $m->district_id = 1;
            if ($m->sub_county_id != null) {
                $sub = Location::find($m->sub_county_id);
                if ($sub != null) {
                    $m->district_id = $sub->parent;
                }
            }

            return $m;
        });
    }

    static function created_suspectes($case)
    {
        if ($case == null) {
            return $case;
        }
        if (!isset($case->id)) {
            return $case;
        }
        $temps = TempData::all();
        foreach ($temps as $key => $tem) {
            $j =  json_decode($tem->data);
            if ($j != null) {
                $s = new CaseSuspect();
                $s->case_id = $case->id;
                $s->uwa_suspect_number = (isset($j->uwa_suspect_number)) ? $j->uwa_suspect_number : null;
                $s->first_name = (isset($j->first_name)) ? $j->first_name : null;
                $s->middle_name = (isset($j->middle_name)) ? $j->middle_name : null;
                $s->last_name = (isset($j->last_name)) ? $j->last_name : null;
                $s->phone_number = (isset($j->phone_number)) ? $j->phone_number : null;
                $s->national_id_number = (isset($j->national_id_number)) ? $j->national_id_number : null;
                $s->sex = (isset($j->sex)) ? $j->sex : null;
                $s->age = (isset($j->age)) ? $j->age : null;
                $s->occuptaion = (isset($j->occuptaion)) ? $j->occuptaion : null;
                $s->country = (isset($j->country)) ? $j->country : null;
                $s->district_id = (isset($j->district_id)) ? $j->district_id : null;
                $s->sub_county_id = (isset($j->sub_county_id)) ? $j->sub_county_id : null;
                $s->parish = (isset($j->parish)) ? $j->parish : null;
                $s->village = (isset($j->village)) ? $j->village : null;
                $s->ethnicity = (isset($j->ethnicity)) ? $j->ethnicity : null;
                $s->finger_prints = (isset($j->finger_prints)) ? $j->finger_prints : null;
                $s->is_suspects_arrested = (isset($j->is_suspects_arrested)) ? $j->is_suspects_arrested : null;
                $s->arrest_date_time = (isset($j->arrest_date_time)) ? $j->arrest_date_time : null;
                $s->arrest_district_id = (isset($j->arrest_district_id)) ? $j->arrest_district_id : null;
                $s->arrest_sub_county_id = (isset($j->arrest_sub_county_id)) ? $j->arrest_sub_county_id : null;
                $s->arrest_parish = (isset($j->arrest_parish)) ? $j->arrest_parish : null;
                $s->arrest_village = (isset($j->arrest_village)) ? $j->arrest_village : null;
                $s->arrest_latitude = (isset($j->arrest_latitude)) ? $j->arrest_latitude : null;
                $s->arrest_longitude = (isset($j->arrest_longitude)) ? $j->arrest_longitude : null;
                $s->arrest_first_police_station = (isset($j->arrest_first_police_station)) ? $j->arrest_first_police_station : null;
                $s->arrest_current_police_station = (isset($j->arrest_current_police_station)) ? $j->arrest_current_police_station : null;
                $s->arrest_agency = (isset($j->arrest_agency)) ? $j->arrest_agency : null;
                $s->arrest_uwa_unit = (isset($j->arrest_uwa_unit)) ? $j->arrest_uwa_unit : null;
                $s->arrest_detection_method = (isset($j->arrest_detection_method)) ? $j->arrest_detection_method : null;
                $s->arrest_uwa_number = (isset($j->arrest_uwa_number)) ? $j->arrest_uwa_number : null;
                $s->arrest_crb_number = (isset($j->arrest_crb_number)) ? $j->arrest_crb_number : null;
                $s->is_suspect_appear_in_court = (isset($j->is_suspect_appear_in_court)) ? $j->is_suspect_appear_in_court : null;
                $s->prosecutor = (isset($j->prosecutor)) ? $j->prosecutor : null;
                $s->is_convicted = (isset($j->is_convicted)) ? $j->is_convicted : null;
                $s->case_outcome = (isset($j->case_outcome)) ? $j->case_outcome : null;
                $s->magistrate_name = (isset($j->magistrate_name)) ? $j->magistrate_name : null;
                $s->court_name = (isset($j->court_name)) ? $j->court_name : null;
                $s->court_file_number = (isset($j->court_file_number)) ? $j->court_file_number : null;
                $s->is_jailed = (isset($j->is_jailed)) ? $j->is_jailed : null;
                $s->jail_period = (isset($j->jail_period)) ? $j->jail_period : null;
                $s->is_fined = (isset($j->is_fined)) ? $j->is_fined : null;
                $s->fined_amount = (isset($j->fined_amount)) ? $j->fined_amount : null;
                $s->status = (isset($j->status)) ? $j->status : null;
                $s->deleted_at = (isset($j->deleted_at)) ? $j->deleted_at : null;
                $s->photo = (isset($j->photo)) ? $j->photo : null;
                $s->court_date = (isset($j->court_date)) ? $j->court_date : null;
                $s->jail_date = (isset($j->jail_date)) ? $j->jail_date : null;
                $s->use_same_arrest_information = (isset($j->use_same_arrest_information)) ? $j->use_same_arrest_information : null;
                $s->use_same_court_information = (isset($j->use_same_court_information)) ? $j->use_same_court_information : null;
                $s->suspect_number = (isset($j->suspect_number)) ? $j->suspect_number : null;
                $s->arrest_in_pa = (isset($j->arrest_in_pa)) ? $j->arrest_in_pa : null;
                $s->pa_id = (isset($j->pa_id)) ? $j->pa_id : null;
                $s->management_action = (isset($j->management_action)) ? $j->management_action : null;
                $s->community_service = (isset($j->community_service)) ? $j->community_service : null;
                $s->save();
            }
            $tem->delete();
        }
    }
    function get_suspect_number()
    {
        $suspects_length = count($this->suspects);
        $suspects_length++;
        return "{$this->case_number}/{$suspects_length}";
    }

    function pa()
    {
        return $this->belongsTo(PA::class, 'pa_id');
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

    function reportor()
    {
        return $this->belongsTo(Administrator::class, 'reported_by');
    }

    function district()
    {
        return $this->belongsTo(Location::class, 'district_id');
    }

    function sub_county()
    {
        return $this->belongsTo(Location::class, 'sub_county_id');
    }

    function exhibits()
    {
        return $this->hasMany(Exhibit::class, 'case_id');
    }

    function comments()
    {
        return $this->hasMany(CaseComment::class, 'case_id');
    }

    function offences()
    {
        return $this->belongsToMany(Offence::class, 'case_has_offences');
    }

    function suspects()
    {
        return $this->hasMany(CaseSuspect::class, 'case_id');
    }


    function getCrbNumber()
    {
        $csb = null;
        foreach ($this->suspects as $key => $suspect) {
            if ($suspect->arrest_crb_number != null) {
                if (strlen($suspect->arrest_crb_number) > 0) {
                    $csb = $suspect->arrest_crb_number;
                    break;
                }
            }
        }
        return $csb;
    }

    function getSdNumber()
    {
        $csb = null;
        foreach ($this->suspects as $key => $suspect) {
            if ($suspect->police_sd_number != null) {
                if (strlen($suspect->police_sd_number) > 0) {
                    $csb = $suspect->police_sd_number;
                    break;
                }
            }
        }
        return $csb;
    }

    function getCourtFileNumber()
    {
        $csb = null;
        foreach ($this->suspects as $key => $suspect) {
            if ($suspect->court_file_number != null) {
                if (strlen($suspect->court_file_number) > 0) {
                    $csb = $suspect->court_file_number;
                    break;
                }
            }
        }
        return $csb;
    }

    public function getSuspectsCountAttribute()
    {
        return CaseSuspect::where('case_id', $this->id)->count();
    }

    public function getExhibitCountAttribute()
    {
        return Exhibit::where('case_id', $this->id)->count();
    }


    public function getCaTextAttribute()
    {
        if ($this->ca == null) {
            return "";
        }
        return $this->ca->name;
    }
    public function getPaTextAttribute()
    {
        if ($this->pa == null) {
            return "";
        }
        return $this->pa->name;
    }

    public function getDistrictTextAttribute()
    {
        if ($this->district == null) {
            return "";
        }
        return  $this->district->name;
    }

    public function  getPhotoAttribute()
    {



        if ($this->exhibits != null) {
            if (!empty($this->exhibits)) {
                if (isset($this->exhibits[0])) {
                    return $this->exhibits[0]->photos;
                }
            }
        }



        if ($this->suspects != null) {
            if (!empty($this->suspects)) {
                if (isset($this->suspects[0]))
                    if ($this->suspects[0]->photo != null) {
                        if (isset($this->suspects[0])) {
                            if (strlen($this->suspects[0]->photo) > 2) {
                                return $this->suspects[0]->photo;
                            }
                        }
                    }
            }
        }

        return "logo.png";
    }

    protected $appends = ['ca_text', 'pa_text', 'district_text', 'photo', 'suspects_count', 'exhibit_count'];
}
