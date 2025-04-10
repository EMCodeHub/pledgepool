<?php

namespace App\Http\Controllers;

use App\Models\InvestmentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Investment Accounts",
 *     description="API Endpoints for managing investment accounts"
 * )
 */
class InvestmentAccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/investment-account",
     *     summary="Get the authenticated user's investment account details",
     *     tags={"Investment Accounts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Investment account details retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getInvestmentAccount(Request $request)
    {
        // Fetch the investment account of the authenticated user
        $investmentAccount = $request->user()->investmentAccount;

        return response()->json([
            'balance' => $investmentAccount->balance,  // Return the balance of the account
            'reserved_amount' => $investmentAccount->reserved_amount,  // Return the reserved amount in the account
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/investment-account/top-up",
     *     summary="Top up the investment account",
     *     tags={"Investment Accounts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", example=100)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Investment account topped up successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function topUpInvestmentAccount(Request $request)
    {
        // Validate the provided amount to ensure it's positive
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Add the provided amount to the user's investment account balance
        $investmentAccount = $request->user()->investmentAccount;
        $investmentAccount->balance += $request->amount;
        $investmentAccount->save();

        // Create a deposit transaction
        $investmentAccount->transactions()->create([
            'amount' => $request->amount,
            'transaction_type' => 'deposit',
        ]);

        return response()->json([
            'message' => 'Investment account topped up successfully.',
            'balance' => $investmentAccount->balance,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/investment-account/withdraw",
     *     summary="Withdraw funds from the investment account",
     *     tags={"Investment Accounts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", example=50)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Funds withdrawn successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=400, description="Insufficient funds"),
     *     @OA\Response(response=404, description="Investment account not found")
     * )
     */
    public function withdrawFunds(Request $request)
    {
        // Validate the withdrawal amount
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);
    
        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Verify that the user is authenticated
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        // Get the authenticated user's investment account
        $investmentAccount = $user->investmentAccount;
        if (!$investmentAccount) {
            return response()->json(['message' => 'Investment account not found'], 404);
        }
    
        // Ensure the user has enough funds to withdraw
        if ($investmentAccount->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }
    
        // Deduct the withdrawal amount from the balance
        $investmentAccount->balance -= $request->amount;
        $investmentAccount->save();
    
        // Create a withdrawal transaction
        $investmentAccount->transactions()->create([
            'amount' => $request->amount,
            'transaction_type' => 'withdrawal',
        ]);
    
        return response()->json([
            'message' => 'Funds withdrawn successfully.',
            'balance' => $investmentAccount->balance,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/investment-account/transactions",
     *     summary="List the last 10 transactions of the investment account",
     *     tags={"Investment Accounts"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of transactions retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function listTransactions(Request $request)
    {
        // Get the user's investment account and fetch the latest 10 transactions
        $investmentAccount = $request->user()->investmentAccount;
        $transactions = $investmentAccount->transactions()->latest()->take(10)->get(); // Fetch latest 10 transactions

        return response()->json($transactions);  // Return the transaction data in JSON format
    }

    /**
     * Common method for amount validation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function validateAmount(Request $request)
    {
        // Validate that the 'amount' field is numeric and greater than or equal to 1
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    }
}
