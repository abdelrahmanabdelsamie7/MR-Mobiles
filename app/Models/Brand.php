<?php
namespace App\Models;
use App\Models\{Mobile, Accessory};
use App\traits\{HasSlug, UsesUuid};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, HasSlug, UsesUuid;
    protected $table = 'brands';
    protected $fillable = ['name', 'slug', 'image'];
    public function getSlugSource()
    {
        return 'name';
    }
    public function mobiles()
    {
        return $this->hasMany(Mobile::class, 'brand_id');
    }
    public function accessories()
    {
        return $this->hasMany(Accessory::class, 'brand_id');
    }
}