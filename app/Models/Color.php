<?php
namespace App\Models;
use App\Models\Mobile;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory,UsesUuid;
    protected $table = 'colors';
    protected $fillable = ['name', 'hex_code'];
    public function mobiles()
    {
        return $this->belongsToMany(Mobile::class, 'mobile_color_variants', 'color_id', 'mobile_id');
    }
}
