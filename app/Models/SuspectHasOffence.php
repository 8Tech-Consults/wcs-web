<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuspectHasOffence extends Model
{
    protected $fillable = [
        'case_id',
        'offence_id',
        'suspect_id', 
    ];  
    use HasFactory;
}
