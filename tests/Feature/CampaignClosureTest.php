<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Investment;
use App\Notifications\CampaignClosed;  // Ensure the notification is imported
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignClosureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testCampaignClosure()
    {
        // Fake the sending of notifications
        Notification::fake();

        // Create a user
        $user = User::factory()->create();

        // Create a campaign of type 'Auction'
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'amount' => 10000,  // Base amount for the campaign
            'contract_fee' => 1000,  // Contract fee
            'target_amount' => 11000,  // Target amount (amount + contract fee)
            'interest_rate' => 10,  // Interest rate for the campaign
            'deadline' => now()->addDays(10),  // Set the campaign deadline 10 days from now
            'type' => 'Auction',  // Type of the campaign (Auction)
        ]);

        // Create some investment offers
        $investment1 = Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 5000,  // Investment amount
            'interest_rate' => 9,  // Interest rate for the investment (acceptable)
        ]);

        $investment2 = Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 6000,  // Investment amount
            'interest_rate' => 8,  // Interest rate for the investment (acceptable)
        ]);

        // Simulate the closing of the campaign
        $campaign->close();

        // Assert that the campaign is successfully closed
        $this->assertTrue($campaign->status === 'closed');  // The campaign should have the status 'closed'

        // Verify that the notifications were sent
        Notification::assertSentTo(
            [$user], CampaignClosed::class, 1  // Check that 1 notification of type CampaignClosed was sent to the user
        );
    }
}
