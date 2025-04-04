<?php
namespace App\Models;
use App\Models\{Mobile,CartItem};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileColor extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'mobile_colors';
    protected $fillable = ['mobile_id', 'color', 'image'];
    public function mobile()
    {
        return $this->belongsTo(Mobile::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_color_id');
    }
}
