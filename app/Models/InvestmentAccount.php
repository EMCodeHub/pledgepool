<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentAccount extends Model
{
    use HasFactory;

    // Fillable properties to allow mass assignment
    protected $fillable = [
        'user_id',         // ID of the user (owner of the investment account)
        'account_number',  // Unique account number for the investment account
        'balance',         // Current balance in the investment account
        'reserved_amount', // Amount reserved in the investment account (e.g., for active investments)
    ];

    /**
     * Relationship with the User model.
     * This defines a one-to-many relationship between InvestmentAccount and User,
     * where each investment account belongs to one user (the account holder).
     */
    public function user()
    {
        return $this->belongsTo(User::class);  // An investment account belongs to a user
    }

    /**
     * Relationship with the Transaction model.
     * This defines a one-to-many relationship between InvestmentAccount and Transaction,
     * where each investment account can have many transactions associated with it.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);  // An investment account has many transactions
    }
}
