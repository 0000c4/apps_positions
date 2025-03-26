<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTopPosition extends Model
{
    protected $fillable = [
        'date', 'app_id', 'country_id', 'category_id', 'position'
    ];
}