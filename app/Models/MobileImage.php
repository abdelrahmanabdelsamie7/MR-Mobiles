<?php

namespace App\Models;

use App\Models\Mobile;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileImage extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'mobile_images';
    protected $fillable = ['mobile_id','image'];
    public function mobile()
    {
        return $this->belongsTo(Mobile::class);
    }
}
