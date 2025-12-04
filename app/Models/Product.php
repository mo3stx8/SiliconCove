<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'restock_level', 'image', 'category'];

    // Automatically return full URL for image
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? Storage::url($this->image) 
            : 'https://dummyimage.com/600x400/55595c/fff';
    }

    /**
     * Scope a query to only include products with low stock.
     * Low stock is defined as stock below or equal to restock_level
     */
    public function scopeWhereLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'restock_level');
    }
}
