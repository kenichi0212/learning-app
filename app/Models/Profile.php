<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'biography',
        'is_camera_enabled', 
        'is_screenshot_enabled', 
        'image_path',
    ];
}
