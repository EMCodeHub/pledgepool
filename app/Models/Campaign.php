<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\CampaignClosed;

class Campaign extends Model
{
    use HasFactory;

    // Fillable properties to allow mass assignment
    protected $fillable = [
        'owner_id',            // ID of the campaign owner (User)
        'name',                // Name of the campaign
        'amount',              // Amount for the campaign
        'contract_fee',        // Contract fee for the campaign
        'interest_rate',       // Interest rate for the campaign
        'campaign_type',       // Type of the campaign (e.g., loan, investment)
        'deadline',            // Deadline for the campaign
        'loan_duration',       // Duration of the loan (if applicable)
        'target_amount',       // Target amount to be raised in the campaign
        'status',              // Status of the campaign (e.g., active, closed)
    ];

    /**
     * Method to close the campaign.
     * This method updates the campaign status to 'closed' and sends a notification to the owner.
     */
    public function close()
    {
        // Update the campaign status to 'closed'
        $this->status = 'closed';
        $this->save(); // Save the changes to the database

        // Send a notification to the owner of the campaign
        $this->owner->notify(new CampaignClosed());  // Ensure the 'owner' relation is defined in the model
    }

    /**
     * Relationship with the User model (campaign owner).
     * This defines a one-to-many relationship between Campaign and User, 
     * where the campaign belongs to one user (the owner).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');  // The campaign belongs to a user (owner)
    }

    /**
     * Relationship with the Investment model.
     * This defines a one-to-many relationship between Campaign and Investment,
     * where one campaign can have many investments.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);  // A campaign can have multiple investments
    }
}
