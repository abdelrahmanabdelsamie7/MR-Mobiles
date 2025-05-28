<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{Accessory,Color, AccessoryVariantImage, CartItem};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessoryColorVariant extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'accessory_color_variants';
    protected $fillable = ['accessory_id', 'color_id', 'stock_quantity'];
    public function hasStock(): bool
    {
        return $this->stock_quantity > 0;
    }
    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function images()
    {
        return $this->hasMany(AccessoryVariantImage::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_color_id');
    }
}
