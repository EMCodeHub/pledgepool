<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InvestmentAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InvestmentAccountTest extends TestCase
{
    use RefreshDatabase;

    // Create a user and an investment account before each test
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user with a specified email and password
        $this->user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create an investment account associated with the user
        $this->investmentAccount = InvestmentAccount::create([
            'user_id' => $this->user->id,
            'balance' => 1000,
            'reserved_amount' => 500,
        ]);
    }

    /**
     * Test to get the investment account details
     *
     * @return void
     */
    public function test_get_investment_account()
    {
        // Send a GET request to fetch the user's investment account
        $response = $this->actingAs($this->user)->getJson('/api/investment-account');

        // Assert that the response has a 200 status code and the correct balance and reserved amount
        $response->assertStatus(200)
                 ->assertJson([
                     'balance' => $this->investmentAccount->balance,
                     'reserved_amount' => $this->investmentAccount->reserved_amount,
                 ]);
    }

    /**
     * Test to top-up the investment account
     *
     * @return void
     */
    public function test_top_up_investment_account()
    {
        // Send a POST request to top-up the investment account
        $response = $this->actingAs($this->user)->postJson('/api/investment-account/top-up', [
            'amount' => 500,  // Amount to top up the account
        ]);

        // Assert that the response has a 200 status code and the correct message and updated balance
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Investment account topped up successfully.',
                     'balance' => 1500, // Original balance + top-up amount
                 ]);

        // Verify that a transaction was created for the top-up
        $this->assertDatabaseHas('transactions', [
            'investment_account_id' => $this->investmentAccount->id,
            'amount' => 500.00,  // Ensure correct decimal format
            'transaction_type' => 'deposit',  // The transaction type is 'deposit'
        ]);
    }

    /**
     * Test to withdraw funds from the investment account
     *
     * @return void
     */
    public function test_withdraw_funds_from_investment_account()
    {
        // Send a POST request to withdraw funds from the investment account
        $response = $this->actingAs($this->user)->postJson('/api/investment-account/withdraw', [
            'amount' => 200,  // Amount to withdraw from the account
        ]);

        // Assert that the response has a 200 status code and the correct message and updated balance
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Funds withdrawn successfully.',
                     'balance' => 800, // Original balance - withdrawal amount
                 ]);

        // Verify that a transaction was created for the withdrawal
        $this->assertDatabaseHas('transactions', [
            'investment_account_id' => $this->investmentAccount->id,
            'amount' => 200.00,  // Ensure correct decimal format
            'transaction_type' => 'withdrawal',  // The transaction type is 'withdrawal'
        ]);
    }

    /**
     * Test to list the transactions for the investment account
     *
     * @return void
     */
    public function test_list_transactions()
    {
        // Create a test transaction for the investment account
        $this->investmentAccount->transactions()->create([
            'amount' => 100,
            'transaction_type' => 'deposit',
        ]);

        // Send a GET request to fetch the list of transactions
        $response = $this->actingAs($this->user)->getJson('/api/investment-account/transactions');

        // Assert that the response has a 200 status code and contains at least one transaction
        $response->assertStatus(200)
                 ->assertJsonCount(1);  // Verify that there is at least one transaction
    }
}
