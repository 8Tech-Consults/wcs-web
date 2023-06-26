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

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'pa_id');
    }
}
