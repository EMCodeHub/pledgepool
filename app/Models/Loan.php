<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Fillable properties for mass assignment
    protected $fillable = [
        'campaign_id',   // ID of the campaign associated with the loan
        'user_id',       // ID of the user who took out the loan
        'amount',        // The amount of the loan
        'target_amount', // The target amount to be raised (if applicable)
        'interest_rate', // The interest rate of the loan
        'duration',      // Duration of the loan in months/years
        'start_date',    // Start date of the loan
    ];

    /**
     * Relationship with the Campaign model.
     * This defines a many-to-one relationship where each loan is associated with one campaign.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);  // A loan belongs to a campaign
    }

    /**
     * Relationship with the AmortizationSchedule model.
     * This defines a one-to-many relationship where each loan can have multiple amortization schedules.
     */
    public function amortizationSchedule()
    {
        return $this->hasMany(AmortizationSchedule::class);  // A loan can have many amortization schedules
    }
}
