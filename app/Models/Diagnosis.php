<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_layanan',
        'diagnosa_singkat',
        'screening_answers',
        'screening_result',
        'survey_facilities',
        'survey_cleanliness',
        'survey_doctor',
        'survey_pharmacy',
        'status_survei',
        'verification_status',
        'verified_penyakit',
        'catatan_dokter',
        'verified_by',
        'verified_at',
        'profit_amount',
    ];

    protected $casts = [
        'screening_answers' => 'array',
        'profit_amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
