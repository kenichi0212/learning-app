<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningLog extends Model
{
    protected $fillable = [
        'learning_session_id',
        'captured_at',
        'is_away',
        'is_irrelevant',
    ];
}
