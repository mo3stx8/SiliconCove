<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/OrderActivity.php
class OrderActivity extends Model
{
    protected $fillable = [
        'order_id',
        'description',
        'icon',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

