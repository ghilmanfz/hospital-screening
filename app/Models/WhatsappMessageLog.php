<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappMessageLog extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'message_type',
        'message_content',
        'provider',
        'provider_response',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
