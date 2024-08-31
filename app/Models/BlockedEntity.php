<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedEntity extends Model
{
    protected $fillable = ['email', 'ip', 'blocked_until'];

    protected $casts = [
        'blocked_until' => 'datetime',
    ];

    public static function isBlocked($email, $ip)
    {
        $now = now();
        $query = static::where(function ($query) use ($email, $ip) {
            $query->where('email', $email)->orWhere('ip', $ip);
        })->where('blocked_until', '>', $now);
        return $query->exists();
    }


    public static function block($email, $ip, $duration)
    {
        // dd($email, $ip, $duration);
        return static::updateOrCreate(
            ['email' => $email, 'ip' => $ip],
            ['blocked_until' => now()->addHours($duration)]
        );
    }
}
