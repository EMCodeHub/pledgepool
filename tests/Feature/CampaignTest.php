<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_a_user_can_create_a_campaign()
    {
        // Create a user to associate with the campaign
        $user = User::factory()->create();
    
        // Create an authentication token for the user
        $token = $user->createToken('TestToken')->plainTextToken;
    
        // Send a POST request to create the campaign, including the necessary fields
        $response = $this->postJson('/api/campaigns', [
            'name' => 'Eco Solar Project',  // Ensure 'name' is included here
            'amount' => 10000,  // The base amount for the campaign
            'contract_fee' => 500,  // The contract fee for the campaign
            'deadline' => now()->addDays(30)->format('Y-m-d'),  // The deadline for the campaign (30 days from now)
            'loan_duration' => 12,  // The loan duration in months
            'first_name' => 'John',  // First name (assuming this is needed for the campaign)
            'interest_rate' => 5.5,  // The interest rate for the campaign
            'campaign_type' => 'normal', // Type of the campaign should be valid (e.g., 'normal' or 'auction')
        ], [
            'Authorization' => 'Bearer ' . $token,  // Pass the token in the Authorization header
        ]);
    
        // If the response status is 422, print the response content for debugging
        if ($response->status() === 422) {
            dd($response->json());  // Add dd() to check the content of the response for debugging
        }
    
        // Assert that the response status is 201 (successful creation)
        $response->assertStatus(201);
    
        // Verify that the campaign was saved in the database
        $this->assertDatabaseHas('campaigns', [
            'name' => 'Eco Solar Project',  // Check that the campaign's name is saved
            'owner_id' => $user->id,  // Ensure the campaign is associated with the correct user
            'amount' => 10000,  // Check if the campaign's amount is saved
        ]);
    }
}
