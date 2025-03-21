<?php

namespace App\Models;
use App\Models\{User, CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'carts';
    protected $fillable = ['user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
