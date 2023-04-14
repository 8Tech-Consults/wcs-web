<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;
    
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($m) {
            die("You cannot delete this item."); 
        });
    }

    
}
