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
        'case_suspect_id',
        'vadict',
    ];
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($m) {
            $old = SuspectHasOffence::where([
                'offence_id' => $m->offence_id,
                'case_suspect_id' => $m->case_suspect_id,
            ])->first();
            if ($old != null) {
                return false;
            }
        });
    }
}
