<?php
namespace App\Models;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stripe\Climate\Order;

class OrderItem extends Model
{
    use HasFactory , UsesUuid;
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'product_type', 'quantity', 'price'];
    public function product()
    {
        return $this->morphTo();
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
