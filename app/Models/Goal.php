<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'deadline',
        'stop_doing',
        'if_then_normal',
        'if_then_busy',
    ];
}
