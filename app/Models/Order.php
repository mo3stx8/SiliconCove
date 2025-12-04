<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'admin_id',
        'product_id',
        'payment_method',
        'proof_of_payment',
        'quantity',
        'total_amount',
        'status',
        'refund_no',
        'refund_requested_date',
        'refund_reason',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}