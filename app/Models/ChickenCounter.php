<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChickenCounter extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'chicken_counters';
    protected $fillable = [
        'user_id',
        'team_id',
        'total_chicken',
        'total_healthy_chicken',
        'total_unhealthy_chicken',
        'total_unknown_chicken',
    ];
}
