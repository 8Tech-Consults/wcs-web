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
        self::creating(function ($m) {

            $m->district_id = 1;
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

    function pa()
    {
        return $this->belongsTo(PA::class, 'pa_id');
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

    function offences()
    {
        return $this->belongsToMany(Offence::class, 'case_has_offences');
    }

    function suspects()
    {
        return $this->hasMany(CaseSuspect::class, 'case_id');
    }
}
