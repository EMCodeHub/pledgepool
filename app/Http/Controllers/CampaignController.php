<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignFinalizedNotification;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Campaigns",
 *     description="API Endpoints for managing campaigns"
 * )
 */
class CampaignController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/campaigns",
     *     summary="Create a new campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","amount","contract_fee","interest_rate","campaign_type","deadline","loan_duration"},
     *             @OA\Property(property="name", type="string", example="My Campaign"),
     *             @OA\Property(property="amount", type="integer", example=1000),
     *             @OA\Property(property="contract_fee", type="integer", example=100),
     *             @OA\Property(property="interest_rate", type="number", format="float", example=5.5),
     *             @OA\Property(property="campaign_type", type="string", enum={"normal", "auction"}),
     *             @OA\Property(property="deadline", type="string", format="date", example="2025-06-30"),
     *             @OA\Property(property="loan_duration", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Campaign created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createCampaign(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'contract_fee' => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0.0|max:100.0',
            'campaign_type' => 'required|in:normal,auction',
            'deadline' => 'required|date',
            'loan_duration' => 'required|integer|min:1',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the campaign
        $campaign = Campaign::create([
            'owner_id' => $request->user()->id,
            'name' => $request->name,
            'amount' => $request->amount,
            'contract_fee' => $request->contract_fee,
            'interest_rate' => $request->interest_rate,
            'campaign_type' => $request->campaign_type,
            'deadline' => $request->deadline,
            'loan_duration' => $request->loan_duration,
            'target_amount' => $request->amount + $request->contract_fee,
        ]);

        // Return the created campaign
        return response()->json([
            'message' => 'Campaign created successfully.',
            'campaign' => $campaign,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/campaigns",
     *     summary="List all campaigns (paginated)",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Campaign list retrieved")
     * )
     */
    public function listCampaigns()
    {
        // Get the list of campaigns, ordered by creation date
        $campaigns = Campaign::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($campaigns);
    }

    /**
     * @OA\Get(
     *     path="/api/campaigns/{id}",
     *     summary="Get campaign details",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Campaign found"),
     *     @OA\Response(response=404, description="Campaign not found")
     * )
     */
    public function getCampaign($id)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($id);
        return response()->json($campaign);
    }

    /**
     * @OA\Post(
     *     path="/api/campaigns/{id}/close",
     *     summary="Close a campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Campaign closed or cancelled"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function closeCampaign(Request $request, $id)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($id);

        // Check if the user is the owner of the campaign
        if ($campaign->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Determine if the campaign should be closed or cancelled
        if ($campaign->deadline < now() && $campaign->amount < $campaign->target_amount) {
            $campaign->status = 'cancelled';
        } else {
            $campaign->status = 'closed';
        }

        // Save the updated campaign status
        $campaign->save();

        // Get the campaign owner and ensure they have an associated investment account
        $owner = $campaign->owner;
        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $account = $owner->investmentAccount;
        if (!$account) {
            $account = InvestmentAccount::create([
                'user_id' => $owner->id,
                'balance' => 0,
            ]);
        }

        // If the campaign is closed, update the owner's account balance
        if ($campaign->status === 'closed') {
            $account->balance += $campaign->amount;
            $account->save();
        }

        return response()->json([
            'message' => $campaign->status === 'cancelled'
                ? 'Campaign cancelled successfully.'
                : 'Campaign closed successfully.',
            'campaign' => $campaign,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/campaigns/{id}/cancel",
     *     summary="Cancel a campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Campaign cancelled"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function cancelCampaign(Request $request, $id)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($id);

        // Check if the user is the owner of the campaign
        if ($campaign->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Set the campaign status to cancelled
        $campaign->status = 'cancelled';
        $campaign->save();

        // Release reserved amounts for all investments in the campaign
        foreach ($campaign->investments as $investment) {
            $investment->releaseReservedAmount();
        }

        return response()->json([
            'message' => 'Campaign cancelled successfully.',
            'campaign' => $campaign,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/campaigns/{campaignId}/finalize",
     *     summary="Finalize a campaign and create a loan",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="campaignId",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Campaign finalized and loan created"),
     *     @OA\Response(response=400, description="Campaign not fully funded")
     * )
     */
    public function finalizeCampaign(Request $request, $campaignId)
    {
        // Find the campaign by ID
        $campaign = Campaign::findOrFail($campaignId);

        // Ensure the campaign is fully funded
        if ($campaign->amount < $campaign->target_amount) {
            return response()->json(['message' => 'Campaign not fully funded.'], 400);
        }

        // Create a loan for the campaign
        $loan = Loan::create([
            'campaign_id' => $campaign->id,
            'amount' => $campaign->amount,
            'interest_rate' => $campaign->interest_rate,
            'loan_duration' => $campaign->loan_duration,
            'status' => 'active',
        ]);

        // Send notification emails to the campaign owner and investors
        Mail::to($campaign->owner->email)->send(new CampaignFinalizedNotification($campaign));
        foreach ($campaign->investments as $investment) {
            Mail::to($investment->investor->email)->send(new CampaignFinalizedNotification($campaign));
        }

        return response()->json([
            'message' => 'Campaign finalized successfully.',
            'loan' => $loan,
        ]);
    }
}
