<?php
namespace App\Models;
use App\Models\{Order, MobileColorVariant, AccessoryColorVariant};
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
    public function getColorAttribute()
    {
        return $this->product_type === 'mobile'
            ? $this->mobileColor
            : ($this->product_type === 'accessory'
                ? $this->accessoryColor
                : null);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function mobileColor()
    {
        return $this->belongsTo(MobileColorVariant::class, 'product_color_id');
    }
    public function accessoryColor()
    {
        return $this->belongsTo(AccessoryColorVariant::class, 'product_color_id');
    }
}