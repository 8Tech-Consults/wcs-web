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
            die("Ooops! You cannot delete this item.");
        });
        self::created(function ($m) {
            $m->case_number = Utils::getCaseNumber($m);
            $m->save();
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

            return $m;
        });
        self::updating(function ($m) {

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

    protected $appends = ['ca_text', 'pa_text', 'district_text', 'photo'];
}
