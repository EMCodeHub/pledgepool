<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Investment;
use App\Models\InvestmentAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignClosingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function campaign_can_be_closed_successfully_when_target_amount_is_reached()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an investment account for the user
        $investmentAccount = $user->investmentAccount()->create([
            'balance' => 0,  // Set the initial balance to 0
            'reserved_amount' => 0,  // Set the reserved amount to 0
        ]);

        // Create a campaign with a target amount
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'amount' => 10000,  // The amount to be financed
            'contract_fee' => 500,  // The contract fee
            'target_amount' => 10500,  // The target amount (amount + contract fee)
            'deadline' => now()->addDays(30),  // Set the campaign deadline to 30 days from now
            'type' => 'Normal',  // Type of campaign (Normal or Auction)
            'interest_rate' => 5.5,  // Interest rate for the campaign
        ]);

        // Create an investment for the campaign
        Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 10000,  // Investment amount enough to reach the target amount
            'interest_rate' => 5.5,  // Interest rate for the investment
        ]);

        // Simulate the completion of the campaign (close the campaign)
        $response = $this->postJson('/api/campaigns/' . $campaign->id . '/close');  // API endpoint to close the campaign

        // Assert that the campaign was successfully closed
        $response->assertStatus(200);
        $campaign->refresh();
        $this->assertEquals('closed', $campaign->status);  // Ensure the campaign status is 'closed'

        // Verify that the campaign amount was correctly transferred to the investment account
        $this->assertDatabaseHas('investment_accounts', [
            'user_id' => $user->id,
            'balance' => 10000,  // The amount should be deposited into the user's investment account
        ]);
    }

    /** @test */
    public function campaign_can_be_cancelled_if_target_amount_is_not_reached_and_deadline_passed()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user);
    
        // Ensure the user has an investment account
        $user->investmentAccount()->create([
            'balance' => 0, // Set the initial balance to 0
        ]);
    
        // Create a campaign with a target amount but no sufficient investments
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'amount' => 10000,
            'contract_fee' => 500,
            'target_amount' => 10500,
            'deadline' => now()->subDays(1),  // Set the deadline to a past date
            'type' => 'Normal',
            'interest_rate' => 5.5,
        ]);
    
        // Attempt to close the campaign
        $response = $this->postJson('/api/campaigns/' . $campaign->id . '/close');  // API endpoint to close the campaign
    
        // Assert that the campaign was cancelled
        $response->assertStatus(200);
        $campaign->refresh();
        $this->assertEquals('cancelled', $campaign->status);  // Ensure the campaign status is 'cancelled'
    
        // Verify that no investments have been processed for the campaign
        $this->assertDatabaseMissing('investments', [
            'campaign_id' => $campaign->id,
            'status' => 'accepted',  // Ensure no investment was accepted
        ]);
    }
}
