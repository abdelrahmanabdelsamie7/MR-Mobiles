<?php
namespace App\Models;
use App\Models\{OrderItem, User};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, UsesUuid;
    protected $fillable = [
        'user_id',
        'payment_method',
        'payment_status',
        'payment_proof',
        'total_price',
        'note',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
