<?php
namespace App\Models;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory,UsesUuid;
    protected $fillable = ['user_id', 'total_price', 'status'];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
