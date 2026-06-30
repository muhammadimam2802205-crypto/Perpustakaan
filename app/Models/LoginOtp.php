<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginOtp extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'is_verified',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function isValid()
    {
        return !$this->is_verified && $this->expires_at && $this->expires_at->isFuture();
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeValid($query)
    {
        return $query->where('is_verified', false)
                     ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}