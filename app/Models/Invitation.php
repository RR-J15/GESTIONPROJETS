<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'created_by',
        'email',
        'role',
        'token',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }

    // ===== Relations =====

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== Helpers =====

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }
}