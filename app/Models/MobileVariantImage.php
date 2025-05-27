<?php
namespace App\Models;
use App\Models\MobileColorVariant;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MobileVariantImage extends Model
{
    use HasFactory , UsesUuid;
    protected $table = 'mobile_variant_images';
    protected $fillable = ['mobile_color_variant_id', 'image'];
    public function mobileColorVariant()
    {
        return $this->belongsTo(MobileColorVariant::class, 'mobile_color_variant_id');
    }
}