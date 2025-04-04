<?php
namespace App\Models;
use App\Models\{Order,MobileColor};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory , UsesUuid;
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'product_type', 'quantity', 'price' ,'product_color_id'];
    public function product()
    {
        return $this->morphTo();
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function productColor()
    {
        return $this->belongsTo(MobileColor::class, 'product_color_id');
    }
}
