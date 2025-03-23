<?php
namespace App\Models;
use App\Models\{Brand, Wishlist, CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessory extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'accessories';
    protected $fillable = ['title', 'brand_id', 'description', 'battery', 'color', 'image', 'price', 'discount', 'stock_quantity', 'status', 'rating'];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'product');
    }
    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'product');
    }
}