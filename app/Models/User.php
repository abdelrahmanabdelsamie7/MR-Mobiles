<?php
namespace App\Models;
use App\Models\{Cart, Wishlist};
use App\traits\UsesUuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UsesUuid;
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'city',
        'area',
        'verification_token',
        'verification_token_expires_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
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
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    /* public function payments()
     {
         return $this->hasMany(Payment::class);
     } */

    /* public function getFullAddressAttribute()
     {
         $address = [];
         if ($this->street)
             $address[] = $this->street;
         if ($this->apartment)
             $address[] = 'Apartment: ' . $this->apartment;
         if ($this->floor)
             $address[] = 'Floor: ' . $this->floor;
         if ($this->building)
             $address[] = 'Building: ' . $this->building;
         if ($this->city)
             $address[] = $this->city;
         if ($this->postal_code)
             $address[] = $this->postal_code;
         if ($this->country)
             $address[] = $this->country;
         return implode(', ', $address);
     } */
}
