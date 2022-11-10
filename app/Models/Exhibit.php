<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exhibit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['case_id', 'exhibit_catgory', 'wildlife', 'implements', 'photos', 'description', 'quantity'];
    function case_model()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

  /*   public function setPhotosAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['photos'] = json_encode($pictures);
        }
    }

    public function getPhotosAttribute($pictures)
    { 
        return json_decode($pictures, true);
    } */
}
