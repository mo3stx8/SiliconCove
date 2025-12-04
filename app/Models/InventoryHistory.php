<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $table = 'inventory_history';
    
    protected $fillable = [
        'product_id',
        'quantity_before',
        'quantity_after',
        'purchase_price_before',
        'purchase_price_after',
        'type',
        'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
