<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Administrator
{
    protected $table = 'admin_users';
    use HasFactory; 


    function pa()
    {
        return $this->belongsTo(PA::class, 'pa_id');
    }

    function ca()
    {
        return $this->belongsTo(ConservationArea::class, 'ca_id');
    }
    
}
