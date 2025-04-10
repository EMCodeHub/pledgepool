<?php

namespace App\Http\Controllers;

use App\Notifications\InvestmentCancelledNotification;
use App\Models\Campaign;
use App\Models\Investment;
use App\Models\InvestmentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Investments",
 *     description="API Endpoints for managing investments"
 * )
 */
class InvestmentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/campaigns/{campaignId}/invest",
     *     summary="Invest in a campaign",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="campaignId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "interest_rate"},
     *             @OA\Property(property="amount", type="integer", example=1000),
     *             @OA\Property(property="interest_rate", type="number", format="float", example=5.5)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Investment successfully made"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=400, description="Insufficient funds or other error")
     * )
     */
    public function investInCampaign(Request $request, $campaignId)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($campaignId);

        // Validate the input data to ensure the amount and interest rate are correct
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0.0|max:100.0',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if the user has an investment account and sufficient funds
        $investmentAccount = $request->user()->investmentAccount;
        if (!$investmentAccount) {
            return response()->json(['message' => 'User does not have an investment account.'], 400);
        }

        // Ensure the user has enough funds to invest
        if ($investmentAccount->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient funds in investment account.'], 400);
        }

        // Create the investment record in the database
        $investment = Investment::create([
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'status' => 'active', // Investment starts as active
        ]);

        // Decrement the balance and increment the reserved amount in the investment account
        $investmentAccount->decrement('balance', $request->amount);
        $investmentAccount->increment('reserved_amount', $request->amount);

        return response()->json([
            'message' => 'Investment successfully made.',
            'investment' => $investment,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/investments",
     *     summary="List the investments of the authenticated user",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of investments retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function listInvestments(Request $request)
    {
        // Fetch the investments for the authenticated user and paginate results
        $investments = Investment::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($investments);
    }

    /**
     * @OA\Get(
     *     path="/api/campaigns/{campaignId}/investments",
     *     summary="List investments for a specific campaign",
     *     tags={"Investments"},
     *     @OA\Parameter(
     *         name="campaignId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of investments retrieved"),
     *     @OA\Response(response=404, description="Campaign not found")
     * )
     */
    public function listCampaignInvestments($campaignId)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($campaignId);

        // Fetch the investments for the given campaign
        $investments = $campaign->investments()->paginate(10);

        return response()->json($investments);
    }

    /**
     * @OA\Post(
     *     path="/api/investments/{investmentId}/cancel",
     *     summary="Cancel an investment",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="investmentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Investment cancelled successfully"),
     *     @OA\Response(response=400, description="Invalid state or already cancelled"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function cancelInvestment(Request $request, $investmentId)
    {
        // Find the investment by ID
        $investment = Investment::findOrFail($investmentId);

        // Ensure the authenticated user is the owner of the investment
        if ($investment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure the investment has not already been cancelled
        if ($investment->status === 'cancelled') {
            return response()->json(['message' => 'Investment already cancelled.'], 400);
        }

        // Check if the investment can be cancelled (only reserved investments can be cancelled)
        if ($investment->status !== 'reserved') {
            return response()->json(['message' => 'Investment cannot be cancelled in current state.'], 400);
        }

        // Update the investment status to cancelled
        $investment->status = 'cancelled';
        $investment->save();

        // Get the user's investment account
        $investmentAccount = $request->user()->investmentAccount;
        if (!$investmentAccount) {
            return response()->json(['message' => 'User does not have an investment account.'], 400);
        }

        // Release the reserved funds from the investment account
        $investmentAccount->decrement('reserved_amount', $investment->amount);
        $investmentAccount->increment('balance', $investment->amount);

        // Send a notification to the user about the cancellation
        $investment->user->notify(new InvestmentCancelledNotification($investment));

        return response()->json([
            'message' => 'Investment cancelled successfully.',
            'investment' => $investment,
        ]);
    }
}
