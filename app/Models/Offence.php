<?php

namespace App\Models;

use App\Admin\Controllers\SuspectCourtStatusController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offence extends Model
{
    use HasFactory;

    public function courtCases() 
    {
        return $this->belongsToMany(CourtCase::class, 'court_has_offences', 'offence_id', 'case_model_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(Suspect::class, 'suspect_has_offences', 'offence_id', 'case_suspect_id');
    }

}
