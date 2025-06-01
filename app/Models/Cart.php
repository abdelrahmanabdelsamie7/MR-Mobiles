<?php
namespace App\Models;
use App\Models\{User, CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cart extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'carts';
    protected $fillable = ['user_id', 'total_price', 'total_quantity'];
    public function updateTotalPrice()
    {
        $this->total_price = $this->items->sum(fn($item) => $item->price * $item->quantity);
        $this->total_quantity = $this->items->sum('quantity');
        $this->save();
    }
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    // public function payment()
    // {
    //     return $this->hasOne(Payment::class);
    // }
}