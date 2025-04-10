<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvestmentAccountController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\InvestmentController;

// Route to register a new user
Route::post('register', [UserController::class, 'register']);

// Route to login an existing user
Route::post('login', [UserController::class, 'login']);

// Logout route with authentication middleware
Route::middleware('auth:sanctum')->post('logout', [UserController::class, 'logout']);

// Protected routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
  
    // Get the investment account details
    Route::get('investment-account', [InvestmentAccountController::class, 'getInvestmentAccount']);

    // Top-up the investment account
    Route::post('investment-account/top-up', [InvestmentAccountController::class, 'topUpInvestmentAccount']);

    // Withdraw funds from the investment account
    Route::post('investment-account/withdraw', [InvestmentAccountController::class, 'withdrawFunds']);

    // Get transaction history of the investment account
    Route::get('investment-account/transactions', [InvestmentAccountController::class, 'listTransactions']);

    // Create a new campaign
    Route::post('campaigns', [CampaignController::class, 'createCampaign']);

    // List all campaigns
    Route::get('campaigns', [CampaignController::class, 'listCampaigns']);

    // Get details of a specific campaign by ID
    Route::get('campaigns/{id}', [CampaignController::class, 'getCampaign']);

    // Close a specific campaign by ID
    Route::post('campaigns/{id}/close', [CampaignController::class, 'closeCampaign']);

    // Cancel a specific campaign by ID
    Route::post('campaigns/{id}/cancel', [CampaignController::class, 'cancelCampaign']);

    // Invest in a specific campaign
    Route::post('campaigns/{campaignId}/invest', [InvestmentController::class, 'investInCampaign']);

    // List all investments
    Route::get('investments', [InvestmentController::class, 'listInvestments']);

    // List all investments in a specific campaign
    Route::get('campaigns/{campaignId}/investments', [InvestmentController::class, 'listCampaignInvestments']);

    // Cancel a specific investment by ID
    Route::post('investments/{investmentId}/cancel', [InvestmentController::class, 'cancelInvestment']);
});
