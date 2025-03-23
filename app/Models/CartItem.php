<?php
namespace App\Models;
use App\Models\Cart;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;
    use UsesUuid ;
    protected $table = 'cart_items';
    protected $fillable = ['cart_id', 'product_id', 'product_type', 'quantity', 'price'];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function product()
    {
        return $this->morphTo();
    }
}