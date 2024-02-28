<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConservationArea extends Model
{
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

    public function pa()
    {
        return $this->hasMany(PA::class, 'ca_id');
    }

    public function cases()
    {
        return $this->hasManyThrough(CaseModel::class, PA::class, 'ca_id', 'pa_id');
    }

    public function get_default_pa()
    {
        $pa = PA::where([
            'name' => $this->name,
            'ca_id' => $this->id,
        ])->first();
        if ($pa == null) {
            $pa = new PA();
            $pa->name = $this->name;
            $pa->created_at = $this->created_at;
            $pa->updated_at = $this->updated_at;
            $pa->subcounty = $this->name;
            $pa->details = $this->description;
            $pa->ca_id = $this->id;
            $pa->short_name = $this->name;
            $pa->save();
        }
        $pa = PA::where([
            'name' => $this->name,
            'ca_id' => $this->id,
        ])->first();
        if ($pa == null) {
            throw new \Exception("Failed to create a default PA for this $this->name");
        }
        return $pa;
    }
    /* 	

    */
}
