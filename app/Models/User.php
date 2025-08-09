<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_domain',
        'password',
        'mobile',
    ];

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
    public function licensePlates()
    {
        return $this->hasMany(LicensePlate::class);
    }
    public function getNameAttribute($value)
    {
        // Check current request path
        if (!request()->is('profile/edit')) {
            // Only run formatting when NOT on profile/edit
            $parts = explode(' ', $value);
            $firstPart = $parts[0] ?? '';
            return ucfirst(strtolower($firstPart));
        }

        // Otherwise, just return the original value
        return $value;
    }
    public function getMobileAttribute($value)
    {
        // If starts with +92, replace with 0
        if (strpos($value, '+92') === 0) {
            return '0' . substr($value, 3); // remove +92 and prepend 0
        }
        return $value;
    }
}
