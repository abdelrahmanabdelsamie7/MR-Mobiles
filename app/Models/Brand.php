<?php
namespace App\Models;
use App\Models\{Mobile, Accessory};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    use UsesUuid;
    protected $table = 'brands';
    protected $fillable = ['name', 'image'];
    public function mobiles()
    {
        return $this->hasMany(Mobile::class);
    }
    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }
}
