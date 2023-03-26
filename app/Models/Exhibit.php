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
        $case = CaseModel::find($this->case_id);
        if ($case == null) {
            $this->delete();
            return '-';
        }
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function setPicsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['pics'] = json_encode($pictures);
        }
    }


    public function setImplementAttachmentsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['implement_attachments'] = json_encode($pictures);
        }
    }


    public function getImplementAttachmentsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }


    public function setOthersAttachmentsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['others_attachments'] = json_encode($pictures);
        }
    }


    public function getOthersAttachmentsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }


    public function setWildlifeAttachmentsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['wildlife_attachments'] = json_encode($pictures);
        }
    }


    public function getWildlifeAttachmentsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function getPicsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }
}
