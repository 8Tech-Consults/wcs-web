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
            die("You can't delete this item.");
        });
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'pa_id');
    }


    public function getNameTextAttribute()
    {
        if (((int)($this->subcounty)) > 0) {
            $mother = Location::find($this->subcounty);

            if ($mother != null) {
                return  $this->name . ' - ' . $mother->name_text;
            }
        }
        return $this->name;
    }


    protected $appends = ['name_text'];
}
