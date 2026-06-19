<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'phone', 'address', 'saldo', 'account_no', 'umur', 'gender', 'status_pekerjaan', 'universitas', 'fakultas', 'pendidikan_terakhir'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'petugas']);
    }

    public function requiresVerificationApproval(): bool
    {
        return $this->role === 'nasabah' && $this->status !== 'verified';
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
            'saldo' => 'integer',
            'umur' => 'integer',
        ];
    }

    /**
     * Get the deposits for the user.
     */
    public function deposits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get the withdrawals for the user.
     */
    public function withdrawals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the savings targets for the user.
     */
    public function savingsTargets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SavingsTarget::class);
    }

    /**
     * Get the pickup requests for the user.
     */
    public function pickupRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PickupRequest::class);
    }
}
