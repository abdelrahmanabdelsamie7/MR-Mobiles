<?php
namespace App\Models;
use App\Models\{Cart,Wishlist};
use App\traits\UsesUuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable , UsesUuid;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // User Relationships
    protected static function boot()
    {
        parent::boot();
        static::created(function ($user) {
            $user->cart()->create();
        });
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
