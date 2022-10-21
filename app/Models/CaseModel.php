<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseModel extends Model
{
    use HasFactory;


 
    public static function boot()
    {
        parent::boot();
        self::creating(function ($m) {
            if (isset($m->location_picker)) {
                unset($m->location_picker);
            }
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
    function suspects()
    {
        return $this->hasMany(CaseSuspect::class, 'case_id');
    }
}
