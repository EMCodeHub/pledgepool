<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Mass assignable properties
    protected $fillable = [
        'first_name',  // User's first name
        'last_name',   // User's last name
        'email',       // User's email address
        'password',    // User's password
    ];

    // Hidden attributes (not exposed when the model is serialized)
    protected $hidden = [
        'password',      // Hide the password when serializing the user
        'remember_token', // Token used for session persistence
    ];

    // Cast attributes to specific data types
    protected $casts = [
        'email_verified_at' => 'datetime',  // Cast email verification date to a datetime format
        'password' => 'hashed',  // Ensure password is always hashed
    ];

    /**
     * Relationship with the investment account.
     * A user has one investment account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function investmentAccount()
    {
        return $this->hasOne(InvestmentAccount::class); // A user has one investment account
    }

    /**
     * If a user had more than one investment account, use this function:
     * public function investmentAccounts()
     * {
     *     return $this->hasMany(InvestmentAccount::class);
     * }
     */
}
