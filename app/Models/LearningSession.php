<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningSession extends Model
{
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'total_duration',
        'effective_duration',
        'session_status',
    ];
}
