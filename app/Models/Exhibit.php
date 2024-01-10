<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Exhibit extends Model
{
    use HasFactory;


    protected $fillable = ['case_id', 'exhibit_catgory', 'wildlife', 'implements', 'photos', 'description', 'quantity'];
    function case_model()
    {
        $case = CaseModel::find($this->case_id);
        if ($case == null) {
            $this->delete();
            return null;
        }
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function setPicsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['pics'] = json_encode($pictures);
        }
    }
    public function get_photos()
    {
        $pics = [];
        if ($this->wildlife_attachments != null && is_array($this->wildlife_attachments)) {
            foreach ($this->wildlife_attachments as $key => $img) {
                $pics[] = url('public/storage/' . $img);
            }
        }
        if ($this->implement_attachments != null && is_array($this->implement_attachments)) {
            foreach ($this->implement_attachments as $key => $img) {
                $pics[] = url('public/storage/' . $img);
            }
        }
        if ($this->others_attachments != null && is_array($this->others_attachments)) {
            foreach ($this->others_attachments as $key => $img) {
                $pics[] = url('public/storage/' . $img);
            }
        }

        return $pics;
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

    public function getPhotosAttribute()
    {
        if (is_array($this->wildlife_attachments)) {
            if (isset($this->wildlife_attachments[0])) {
                return ($this->wildlife_attachments[rand(0,(count($this->wildlife_attachments)-1))]);
            }
        }

        if (is_array($this->others_attachments)) {
            if (!empty($this->others_attachments[0])) {
                return ($this->others_attachments[rand(0,(count($this->others_attachments)-1))]);
            }
        }

        if (is_array($this->implement_attachments)) {
            if (isset($this->implement_attachments[0])) {
                return ($this->implement_attachments[rand(0,(count($this->implement_attachments)-1))]);
            }
        }
    }


    public function setOthersAttachmentsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['others_attachments'] = json_encode($pictures);
        }
    }


    public function getOthersAttachmentsAttribute($pictures)
    {
        if ($pictures != null)
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
        if ($pictures != null)
            return json_decode($pictures, true);
    }

    public function get_species()
    {
        if ($this->wildlife_species == null) {
            return "-";
        }
        $an = Animal::find($this->wildlife_species);
        if ($an != null) {
            return $an->name;
        }
        return $this->wildlife_species;
    }
    public function get_implement()
    {
        if ($this->implement_name == null) {
            return "-";
        }
        $an = ImplementType::find($this->implement_name);
        if ($an != null) {
            return $an->name;
        }
        return $this->implement_name;
    }

    public function getPicsAttribute($pictures)
    {
        if ($pictures != null)
            return json_decode($pictures, true);
    }
}
