<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'total_chicken',
        'total_healthy_chicken',
        'total_unhealthy_chicken',
        'encrypted_image',
        'recognition_result_encrypted',
    ];
}
