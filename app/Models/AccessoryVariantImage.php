<?php
namespace App\Models;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AccessoryVariantImage extends Model
{
    use HasFactory , UsesUuid;
    protected $table = 'accessory_variant_images';
    protected $fillable = ['accessory_color_variant_id', 'image'];
    public function accessoryColorVariant()
    {
        return $this->belongsTo(AccessoryColorVariant::class, 'accessory_color_variant_id');
    }
}
