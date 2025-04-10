<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InvestmentAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for user management"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "first_name", "last_name", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',  // Ensure email is unique
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',  // Confirm password match
        ]);

        // If validation fails, return validation errors
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),  // Return errors in the expected structure
            ], 422);
        }

        // Create the user record in the database
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),  // Hash the password for security
        ]);

        // Create a default investment account for the user
        $investmentAccount = InvestmentAccount::create([
            'user_id' => $user->id,
            'balance' => 0,  // Initial balance
            'reserved_amount' => 0,  // No reserved funds initially
            'currency' => 'EUR',  // Default currency set to EUR
        ]);

        // Return a success response with the user information
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user and generate a token",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful, token returned"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */


    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',  // Ensure email is valid
            'password' => 'required|string',  // Ensure password is provided
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),  // Return errors in the expected structure
            ], 422);  // Validation error status code
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If user not found or password doesn't match, return an error
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);  // Unauthorized
        }

        // Generate an authentication token (using Laravel Passport or Sanctum)
        $token = $user->createToken('PledgePoolToken')->plainTextToken;

        // Return the generated token to the client
        return response()->json(['token' => $token], 200);  // Success
    }

   


    /**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout a user and revoke the authentication token",
 *     tags={"Users"},
 *     @OA\Response(response=200, description="Logged out successfully"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
public function logout(Request $request)
{
    // Revoke the current access token of the user
    $request->user()->currentAccessToken()->delete();

    // Return a success response after logout
    return response()->json(['message' => 'Logged out successfully'], 200);  // Success
}

}
