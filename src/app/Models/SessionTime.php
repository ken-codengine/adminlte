<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTime extends Model
{
    use HasFactory;

    public static function getSessionTimes()
    {
        return self::select('session_time')
            ->get()
            ->map(function ($session) {
                return $session['session_time'];
            })
            ->all();
    }
}
