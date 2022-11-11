<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseSuspectsComment extends Model
{
    use HasFactory;

    protected $table = 'case_suspects_comments';
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'suspect_id',
        'comment_by',
        'body',
    ];

    function suspect()
    {
        return $this->belongsTo(CaseSuspect::class, 'suspect_id');
    }

    function reporter()
    {
        return $this->belongsTo(CaseSuspect::class, 'comment_by');
    }
}
