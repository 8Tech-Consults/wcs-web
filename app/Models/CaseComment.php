<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseComment extends Model
{
    use HasFactory;
    protected $table = 'case_comments';
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'case_id',
        'comment_by',
        'body',
    ];

    function case_model()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    function reporter()
    {
        return $this->belongsTo(Administrator::class, 'comment_by');
    }
}
