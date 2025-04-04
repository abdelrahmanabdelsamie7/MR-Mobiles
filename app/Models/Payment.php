<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
  use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'order_id',
        'user_id',
        'amount',
        'paymob_order_id',
        'status',
        'payment_method',
        'transaction_id',
        'error_message',
        'metadata',
        'paid_at'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'json',
        'paid_at' => 'datetime'
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}