<?php
namespace App\Models;
use App\Models\{Order, MobileColorVariant};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, UsesUuid;
    protected $fillable = [
        'order_id',
        'product_id',
        'product_type',
        'quantity',
        'price',
        'product_color_id',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function color()
    {
        return $this->belongsTo(MobileColorVariant::class, 'product_color_id');
    }
}