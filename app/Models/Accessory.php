<?php
namespace App\Models;
use App\Models\{Brand, Category, Wishlist,CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessory extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'accessories';
    protected $fillable = ['title', 'brand_id', 'category_id', 'description', 'battery', 'color', 'image', 'price', 'discount', 'stock_quantity', 'status', 'rating'];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
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