<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public static function isHoliday($date)
    {
        return self::where('date', $date)->exists();
    }
}
