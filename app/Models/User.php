<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_image',
        'password',
        'email_verified_at',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function sellerApplications()
    {
        return $this->hasMany(SellerApplication::class);
    }

    public function reviewedApplications()
    {
        return $this->hasMany(SellerApplication::class, 'reviewed_by');
    }

    public function statusChanges()
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function deliveryAssignments()
    {
        return $this->hasMany(DeliveryAssignment::class, 'delivery_user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(
            Conversation::class,
            'conversation_participants'
        );
    }

    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class);
    }

    public function hasRole(string $role): bool
    {
        return $this
            ->roles()
            ->where('name', $role)
            ->exists();
    }

    public function assignRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->firstOrFail();

        $this->roles()->syncWithoutDetaching($roleModel->id);
    }

    public function removeRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->first();

        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }
    }
}
