<?php
namespace App\Models;
use App\Models\{
    Mobile,
    CartItem,
    MobileVariantImage,
    Color
};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MobileColorVariant extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'mobile_color_variants';
    protected $fillable = ['mobile_id', 'color_id', 'stock_quantity'];
    public function hasStock(): bool
    {
        return $this->stock_quantity > 0;
    }
    public function mobile()
    {
        return $this->belongsTo(Mobile::class, 'mobile_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function images()
    {
        return $this->hasMany(MobileVariantImage::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_color_id');
    }
}
