<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use App\Traits\{UsesUuid, HasSlug};
use App\Models\{
    Brand,
    OrderItem,
    MobileColorVariant,
    MobileVariantImage,
    Wishlist,
    CartItem
};
class Mobile extends Model
{
    use HasFactory, UsesUuid, HasSlug;
    protected $table = 'mobiles';
    protected $fillable = [
        'title',
        'slug',
        'brand_id',
        'description',
        'model_number',
        'battery',
        'processor',
        'storage',
        'display',
        'image_cover',
        'price',
        'discount',
        'operating_system',
        'camera',
        'network_support',
        'release_year',
        'status',
        'product_type',
        'final_price'
    ];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($mobile) {
            $mobile->loadMissing(['variantImages']);
            foreach ($mobile->variantImages as $image) {
                if ($image->image && basename($image->image) !== 'default.jpg') {
                    File::delete(public_path("uploads/mobiles/" . basename($image->image)));
                }
            }
            if ($mobile->image_cover && !str_contains($mobile->image_cover, 'default.jpg')) {
                File::delete(public_path("uploads/mobiles/" . basename($mobile->image_cover)));
            }
            $mobile->variantImages()->delete();
            $mobile->colorVariants()->delete();
        });
    }
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
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function colorVariants()
    {
        return $this->hasMany(MobileColorVariant::class);
    }
    public function variantImages()
    {
        return $this->hasManyThrough(
            MobileVariantImage::class,
            MobileColorVariant::class,
            'mobile_id',
            'mobile_color_variant_id',
            'id',
            'id'
        );
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