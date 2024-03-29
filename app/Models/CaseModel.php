<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseModel extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;


    //created_by_ca
    public function created_by_ca()
    {
        return $this->belongsTo(ConservationArea::class, 'created_by_ca_id');
    }

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
            CaseComment::where([
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

            $m->district_id = 0;
            $m->has_exhibits = 0;

            if ($m->sub_county_id != null) {
                $sub = Location::find($m->sub_county_id);
                if ($sub != null) {
                    $m->district_id = $sub->parent;
                }
            }
            //$m->offence_description = $m->title;
            $m->case_step = 1;


            $ca = null;
            if (((int)$m->ca_id) > 1) {
                $ca = ConservationArea::find($m->ca_id);
                if ($ca != null) {
                    if ($m->pa_id == 1 || $m->pa_id == null || (int)($m->pa_id) < 2 || $m->pa_id == 0 || $m->pa_id == "") {
                        $pa = $ca->get_default_pa();
                        if ($pa != null) {
                            $m->pa_id = $pa->id;
                            $m->is_offence_committed_in_pa == 'Yes';
                        }
                    }
                }
            }

            $pa = PA::find($m->pa_id);
            if ($pa == null || $pa->id == 1) {
                $m->pa_id = 1;
                $m->ca_id = 1;
                $m->is_offence_committed_in_pa == 'No';
            } else {
                $m->is_offence_committed_in_pa == 'Yes';
                $m->pa_id = $pa->id;
                $m->ca_id = $pa->ca_id;
            }

            if (
                $pa != null
            ) {
                if ($pa->id != 1) {
                    $m->is_offence_committed_in_pa = 'Yes';
                } else {
                    $m->is_offence_committed_in_pa = 'No';
                }
                if ($m->ca_id == null || (int)($m->ca_id) < 2) {
                    $m->ca_id = $pa->ca_id;
                }
            } else {
                $m->is_offence_committed_in_pa = 'No';
                $m->pa_id = 1;
                if ($m->ca_id == null || (int)($m->ca_id) < 2) {
                    $m->ca_id = 1;
                }
            }

            if ($m->pa_id == 1) {
                $m->is_offence_committed_in_pa = 'No';
            }

            $by = User::find($m->reported_by);
            if ($by == null) {
                throw new \Exception("Created by is not a user.");
            }
            $m->created_by_ca_id = $by->ca_id;

            return $m;
        });
        self::updating(function ($m) {
            $ca = null;
            if (((int)$m->ca_id) > 1) {
                $ca = ConservationArea::find($m->ca_id);
                if ($ca != null) {
                    if ($m->pa_id == 1 || $m->pa_id == null || (int)($m->pa_id) < 2 || $m->pa_id == 0 || $m->pa_id == "") {
                        $pa = $ca->get_default_pa();
                        if ($pa != null) {
                            $m->pa_id = $pa->id;
                            $m->is_offence_committed_in_pa == 'Yes';
                        }
                    }
                }
            }

            $pa = PA::find($m->pa_id);
            if ($pa == null || $pa->id == 1) {
                $m->pa_id = 1;
                $m->ca_id = 1;
                $m->is_offence_committed_in_pa == 'No';
            } else {
                $m->is_offence_committed_in_pa == 'Yes';
                $m->pa_id = $pa->id;
                $m->ca_id = $pa->ca_id;
            }


            if (
                $pa != null
            ) {
                $m->is_offence_committed_in_pa = 'Yes';
                if ($m->ca_id == null || (int)($m->ca_id) < 2) {
                    $m->ca_id = $pa->ca_id;
                }
                $m->district_id = 0; //Default district is 0
            } else {
                $m->is_offence_committed_in_pa = 'No';
                $m->pa_id = 1;
                if ($m->ca_id == null || (int)($m->ca_id) < 2) {
                    if ($pa != null) {
                        $m->ca_id = $pa->ca_id;
                    } else {
                        $m->ca_id = 1;
                    }
                }
            }

            if ($m->pa_id == 1) {
                $m->is_offence_committed_in_pa = 'No';
            }

            if ($m->sub_county_id != null) {
                $sub = Location::find($m->sub_county_id);
                if ($sub != null) {
                    $m->district_id = $sub->parent;
                }
            }

            if ($m->is_offence_committed_in_pa == 'Yes') {
                $pa = PA::find($m->pa_id);
                if ($pa != null) {
                    $m->ca_id = $pa->ca_id;
                }
            } else {
                $m->ca_id = 1;
                $m->pa_id = 1;
                $m->is_offence_committed_in_pa = 'No';
            }
            $m->case_number = Utils::getCaseNumber($m);
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
    function get_suspect_number($s)
    {
        $suspects = CaseSuspect::where(['case_id' => $this->id])->count();

        $sus_num = $suspects + 1;
        $sus_num = "{$this->case_number}/{$sus_num}";
        $sus_num = str_replace('//', '/', $sus_num);

        return $sus_num;

        //BELOW WAS FAILING IF NEW CASE
        // $sus_num = "";
        // foreach ($suspects as $key => $sus) {
        //     $sus_num = $this->case_number;
        //     $_key = ($key + 1);
        //     $sus_num = "{$this->case_number}/{$_key}";
        //     $sus_num = str_replace('//', '/', $sus_num);
        //     if ($sus->id == $s->id) {
        //         break;
        //     }
        // }
        // return $sus_num;
    }

    function pa()
    {
        $pa = PA::find($this->pa_id);
        if ($pa == null) {
            $this->ca_id = 1;
            $this->pa_id = 1;
            $this->save();
        }
        return $this->belongsTo(PA::class, 'pa_id');
    }
    function ca()
    {
        return $this->belongsTo(ConservationArea::class, 'ca_id');
        return $this->belongsToThrough(ConservationArea::class, PA::class, null, '', [
            ConservationArea::class => 'ca_id'
        ]);
    }

    function reportor()
    {
        $rep = Administrator::find($this->reported_by);
        if ($rep == null) {
            $this->reported_by = 1;
            $this->save();
        }
        return $this->belongsTo(Administrator::class, 'reported_by');
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

    function sub_county()
    {
        $sub = Location::find($this->sub_county_id);
        if ($sub == null) {
            $this->sub_county_id = 0;
            $this->save();
        }
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
            $this->ca_id = 1;
            $this->save();
        }
        return $this->ca->name;
    }
    public function getPaTextAttribute()
    {
        if ($this->pa == null) {
            $this->pa_id = 1;
            $this->save();
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


        $_pics = [];

        if ($this->exhibits != null) {
            if (!empty($this->exhibits)) {
                if (isset($this->exhibits[0])) {
                    if (is_array($this->exhibits[0]->wildlife_attachments)) {
                        foreach ($this->exhibits[0]->wildlife_attachments as $key => $p) {
                            if (str_contains($p, 'files') || str_contains($p, 'images')) {
                                $_pics[] = $p;
                            }
                        }
                    }

                    if (is_array($this->exhibits[0]->implement_attachments)) {
                        foreach ($this->exhibits[0]->implement_attachments as $key => $p) {
                            if (str_contains($p, 'files') || str_contains($p, 'images')) {
                                $_pics[] = $p;
                            }
                        }
                    }

                    if (is_array($this->exhibits[0]->others_attachments)) {
                        foreach ($this->exhibits[0]->others_attachments as $key => $p) {
                            if (str_contains($p, 'files') || str_contains($p, 'images')) {
                                $_pics[] = $p;
                            }
                        }
                    }
                }
            }
        }



        if ($this->suspects != null) {
            if (!empty($this->suspects)) {
                if (isset($this->suspects[0]))
                    if ($this->suspects[0]->photo != null) {
                        if (isset($this->suspects[0])) {
                            if (strlen($this->suspects[0]->photo) > 2) {
                                if (str_contains($this->suspects[0]->photo, 'images')) {
                                    $_pics[] = $this->suspects[0]->photo;
                                }
                            }
                        }
                    }
            }
        }

        if (!empty($_pics)) {
            shuffle($_pics);
            return $_pics[0];
        }

        return "";
    }

    public function getAllOffences()
    {
        $offences = [];
        foreach ($this->suspects as $key => $sus) {
            foreach ($sus->offences as $off) {
                $offences[] = $off;
            }
        }
        return $offences;
    }

    protected $appends = ['ca_text', 'pa_text', 'district_text', 'photo', 'suspects_count', 'exhibit_count'];
}
