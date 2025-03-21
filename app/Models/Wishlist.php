<?php

namespace App\Models;

use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'wishlists';
    protected $fillable = ['user_id','product_id','product_type'];
    public function product()
    {
        return $this->morphTo();
    }
}
