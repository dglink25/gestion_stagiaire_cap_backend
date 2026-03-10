<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model{
    use HasFactory;

    protected $fillable = [
        'email',
        'role',
        'token',
        'invited_by',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function inviter(): BelongsTo {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool{
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isAccepted(): bool {
        return !is_null($this->accepted_at);
    }
}