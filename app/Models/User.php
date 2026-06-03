<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone_number', 'role', 'status', 'phone_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPasien(): bool
    {
        return $this->role === 'pasien';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPendingVerification(): bool
    {
        return $this->status === 'pending_verification';
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
