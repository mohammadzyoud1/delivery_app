<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'delivery_address',
        'status',
        'custom_request',
        'rejection_reason',
        'total_amount',
        'delivery_locked',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'delivery_locked' => 'boolean',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function deliveryAssignment()
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    public function deliveryVerification()
    {
        return $this->hasOne(DeliveryVerification::class);
    }
}