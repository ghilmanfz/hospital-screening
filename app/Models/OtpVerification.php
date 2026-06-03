<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerification extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'otp_hash',
        'purpose',
        'expired_at',
        'used_at',
        'attempt_count',
        'status',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
