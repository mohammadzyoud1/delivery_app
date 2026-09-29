<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // ==================== Fillable ====================

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    // ==================== Casts ====================

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    // ==================== User ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
