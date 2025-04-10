<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    // Fillable properties to allow mass assignment
    protected $fillable = [
        'user_id',         // ID of the user (investor)
        'campaign_id',     // ID of the campaign being invested in
        'amount',          // Amount invested by the user
        'interest_rate',   // Interest rate for the investment
    ];

    /**
     * Relationship with the User model (investor).
     * This defines a one-to-many relationship between Investment and User,
     * where each investment belongs to one user (the investor).
     */
    public function user()
    {
        return $this->belongsTo(User::class);  // An investment belongs to a user (investor)
    }

    /**
     * Relationship with the Campaign model.
     * This defines a one-to-many relationship between Investment and Campaign,
     * where each investment belongs to one campaign.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);  // An investment belongs to a campaign
    }
}
