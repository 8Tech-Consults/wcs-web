<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PA extends Model
{
    protected $table = 'pas';
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($m) {
            if ($m->id == 1) {
                die("Ooops! You cannot delete this item.");
            }
        });
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'pa_id');
    }

    public function ca()
    {
        $ca = ConservationArea::find($this->ca_id);
        if($ca == null){
            $this->ca_id = 1;
            $this->save();  
        }
        return $this->belongsTo(ConservationArea::class, 'ca_id');
    }


    public function getNameTextAttribute()
    {
        $ca_name = "";
        if ($this->ca != null) {
            $ca_name = $this->ca->name . " - ";
        }
        return $ca_name . $this->name;
    }


    protected $appends = ['name_text'];
}
