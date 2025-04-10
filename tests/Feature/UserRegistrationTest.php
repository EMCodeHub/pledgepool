<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a user can successfully register.
     *
     * @return void
     */
    public function test_user_can_register_successfully()
    {
        // Define the registration payload (user data)
        $payload = [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        // Send a POST request to register the user
        $response = $this->postJson('/api/register', $payload);

        // Assert that the response status is 201 (Created) and contains the necessary fields
        $response->assertCreated()
                 ->assertJsonStructure([
                     'message',  // Ensure the response includes a 'message' field
                     'user' => [
                         'id',  // User ID
                         'email',  // User email
                         'first_name',  // User first name
                         'last_name',  // User last name
                     ]
                 ]);

        // Verify the user has been stored in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Verify that an investment account has been created for the new user
        $this->assertDatabaseHas('investment_accounts', [
            'user_id' => User::where('email', 'john.doe@example.com')->first()->id,
            'currency' => 'EUR',  // Assuming default currency is 'EUR'
        ]);
    }

    /**
     * Test if registration fails with missing required fields.
     *
     * @return void
     */
    public function test_registration_fails_with_missing_fields()
    {
        // Send a POST request with empty data (no fields)
        $response = $this->postJson('/api/register', []);

        // Assert that the response status is 422 (Unprocessable Entity) and the validation errors are present
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'first_name', 'last_name', 'password']);
    }

    /**
     * Test if registration fails with a duplicate email.
     *
     * @return void
     */
    public function test_registration_fails_with_duplicate_email()
    {
        // Create a user with the same email to simulate a duplicate registration
        User::factory()->create([
            'email' => 'john.doe@example.com',
        ]);

        // Define the registration payload with the same email
        $payload = [
            'email' => 'john.doe@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        // Send a POST request to register the user
        $response = $this->postJson('/api/register', $payload);

        // Assert that the response status is 422 (Unprocessable Entity) and the validation error for 'email' is present
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
