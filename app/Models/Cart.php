<?php

namespace App\Models;
use App\Models\{User, CartItem,Payment};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'carts';
    protected $fillable = ['user_id','total_price'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function updateTotalPrice()
    {
        $this->total_price = $this->cartItems->sum(fn($item) => $item->price * $item->quantity);
        $this->save();
    }
      public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
