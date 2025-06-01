<?php
namespace App\Models;
use App\Models\{Brand, Wishlist, CartItem, OrderItem, AccessoryColorVariant, AccessoryVariantImage};
use App\traits\{UsesUuid, HasSlug};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessory extends Model
{
    use HasFactory, UsesUuid, HasSlug;
    protected $table = 'accessories';
    protected $fillable = ['title', 'slug', 'brand_id', 'description', 'battery', 'image', 'price', 'discount', 'status', 'product_type', 'final_price'];
    public function getSlugSource()
    {
        return 'title';
    }
    public function getFinalPriceAttribute()
    {
        if ($this->discount) {
            return $this->price - (($this->discount / 100) * $this->price);
        }
        return $this->price;
    }
    public function getTotalQuantityAttribute()
    {
        return $this->colorVariants()->sum('stock_quantity');
    }
    public function colorVariants()
    {
        return $this->hasMany(AccessoryColorVariant::class);
    }
    public function variantImages()
    {
        return $this->hasManyThrough(
            AccessoryVariantImage::class,
            AccessoryColorVariant::class,
            'accessory_id',
            'accessory_color_variant_id',
            'id',
            'id'
        );
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'product');
    }
    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'product');
    }
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'product');
    }
}