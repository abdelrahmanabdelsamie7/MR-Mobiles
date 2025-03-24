<?php
namespace App\Models;
use App\Models\{Cart,Wishlist};
use App\traits\UsesUuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable , UsesUuid;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'verification_token',
        'verification_token_expires_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
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