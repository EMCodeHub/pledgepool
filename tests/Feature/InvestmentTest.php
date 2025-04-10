<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Investment;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\InvestmentCancelledNotification;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_cancel_their_investment()
    {
        // Create a test user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an investment account with a reserved balance
        $user->investmentAccount()->create([
            'balance' => 0,  // Initial balance
            'reserved_amount' => 1000,  // Amount reserved for investment
        ]);

        // Create an active campaign to which the user will invest
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'campaign_type' => 'normal',
            'status' => 'active',
        ]);

        // Create an investment for the user in the campaign with reserved status
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 1000,
            'status' => 'reserved',  // Initial status is 'reserved'
        ]);

        // Assert that the investment's status is 'reserved'
        $this->assertEquals('reserved', $investment->status);

        // Send a request to cancel the investment
        $response = $this->postJson("/api/investments/{$investment->id}/cancel");

        // Check the response status and message
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Investment cancelled successfully.'  // Ensure the success message is returned
        ]);

        // Refresh the investment and check its status has been updated to 'cancelled'
        $investment->refresh();
        $this->assertEquals('cancelled', $investment->status);

        // Refresh the user's investment account and verify the balance is updated
        $user->investmentAccount->refresh();
        $this->assertEquals(1000, $user->investmentAccount->balance);  // The balance should be updated after cancellation
    }

    /** @test */
    public function user_cannot_cancel_an_already_cancelled_investment()
    {
        // Create a test user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an empty investment account for the user
        $user->investmentAccount()->create([
            'balance' => 0,  // Initial balance
            'reserved_amount' => 0,  // No amount reserved
        ]);

        // Create an active campaign
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'campaign_type' => 'normal',
            'status' => 'active',
        ]);

        // Create an investment that is already cancelled
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 1000,
            'status' => 'cancelled',  // Investment already cancelled
        ]);

        // Attempt to cancel the already cancelled investment
        $response = $this->postJson("/api/investments/{$investment->id}/cancel");

        // Assert that the response is a 400 error with the appropriate message
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Investment already cancelled.'  // Ensure the appropriate error message is returned
        ]);
    }

    /** @test */
    public function investment_cancelation_sends_email_to_user()
    {
        // Create a test user and authenticate them
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an investment account with reserved amount
        $user->investmentAccount()->create([
            'balance' => 0,  // Initial balance
            'reserved_amount' => 1000,  // Reserved amount for investment
        ]);

        // Create an active campaign
        $campaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'campaign_type' => 'normal',
            'status' => 'active',
        ]);

        // Create an investment with 'reserved' status
        $investment = Investment::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 1000,
            'status' => 'reserved',  // Initial status is 'reserved'
        ]);

        // Fake notifications to check if the user receives the cancelation email
        Notification::fake();

        // Send a request to cancel the investment
        $response = $this->postJson("/api/investments/{$investment->id}/cancel");

        // Assert that the InvestmentCancelledNotification was sent to the user
        Notification::assertSentTo(
            $user,
            InvestmentCancelledNotification::class,
            function ($notification) use ($investment) {
                // Check that the correct investment ID is included in the notification
                return $notification->investment->id === $investment->id;
            }
        );

        // Assert that the response has a 200 status code and the correct message
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Investment cancelled successfully.'  // Success message should be returned
                 ]);
    }
}
