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
            if($m->id == 1){
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
}
