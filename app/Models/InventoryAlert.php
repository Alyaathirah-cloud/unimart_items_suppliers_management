<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAlert extends Model
{
    use HasFactory;

    protected $table = 'sent_alerts';

    protected $fillable = [
        'item_id',
        'alert_type',
        'sent_on',
    ];

    protected $casts = [
        'sent_on' => 'date',
    ];
}
