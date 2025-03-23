<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Traits\UsesUuid;

class Wishlist extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'wishlists';
    protected $fillable = ['user_id', 'product_id', 'product_type'];
    public function product()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}