<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Fillable properties for mass assignment
    protected $fillable = [
        'investment_account_id', // ID of the related investment account
        'transaction_type',      // Type of the transaction (e.g., deposit, withdrawal)
        'amount',                // Amount involved in the transaction
        'description',           // Description or details about the transaction
        'transaction_date',      // Date when the transaction took place
    ];

    /**
     * Relationship with the InvestmentAccount model.
     * This defines a many-to-one relationship where each transaction belongs to one investment account.
     */
    public function investmentAccount()
    {
        return $this->belongsTo(InvestmentAccount::class);  // A transaction belongs to an investment account
    }
}
