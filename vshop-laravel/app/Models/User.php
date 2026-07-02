<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,  HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
         'type',
        'referral_code',
        'referred_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            do {
                $code = 'USER-' . strtoupper(Str::random(6));
            } while (self::where('referral_code', $code)->exists());

            $user->referral_code = $code;
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

      public function institutionDetails()
    {
        return $this->hasOne(InstitutionDetail::class);
    }

    public function address()
{
    return $this->hasOne(Address::class);
}

public function wallet()
{
    return $this->hasMany(\App\Models\Wallet::class, 'user_id', 'id');
}

}
