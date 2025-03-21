<?php
namespace App\Models;
use App\Models\{Brand,Category,MobileColor,MobileImage,Wishlist,CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mobile extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'mobiles';
    protected $fillable = ['title', 'brand_id', 'category_id', 'description', 'model_number', 'battery', 'processor', 'storage', 'display', 'image_cover', 'price', 'discount', 'operating_system', 'camera', 'network_support', 'release_year', 'stock_quantity', 'status', 'rating'];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function colors()
    {
        return $this->hasMany(MobileColor::class);
    }
    public function images()
    {
        return $this->hasMany(MobileImage::class);
    }
    public function wishlist()
    {
        return $this->morphOne(Wishlist::class, 'product');
    }
    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'product');
    }
}