<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use App\Traits\UsesUuid;
use App\Models\{Brand, Category, MobileColor, MobileImage, Wishlist, CartItem};

class Mobile extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'mobiles';
    protected $fillable = [
        'title', 'brand_id', 'description', 'model_number', 'battery',
        'processor', 'storage', 'display', 'image_cover', 'price',
        'discount', 'operating_system', 'camera', 'network_support',
        'release_year', 'stock_quantity', 'status'
    ];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($mobile) {
            foreach ($mobile->colors as $color) {
                if ($color->image && basename($color->image) !== 'default.jpg') {
                    File::delete(public_path("uploads/mobiles/" . basename($color->image)));
                }
            }
            foreach ($mobile->images as $image) {
                if ($image->image) {
                    File::delete(public_path("uploads/mobiles/" . basename($image->image)));
                }
            }
            $mobile->colors()->delete();
            $mobile->images()->delete();
        });
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function colors()
    {
        return $this->hasMany(MobileColor::class);
    }
    public function images()
    {
        return $this->hasMany(MobileImage::class);
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