<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    protected $fillable = [
        'user_id',
        'diagnosa_singkat',
        'screening_answers',
        'screening_result',
        'survey_facilities',
        'survey_cleanliness',
        'survey_doctor',
        'survey_pharmacy',
        'status_survei',
        'profit_amount',
    ];

    protected $casts = [
        'screening_answers' => 'array',
        'profit_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
