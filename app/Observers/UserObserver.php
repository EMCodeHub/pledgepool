<?php

namespace App\Observers;

use App\Models\User;
use App\Models\InvestmentAccount;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function created(User $user)
    {
        // Automatically create an investment account when a user is created
        $user->investmentAccount()->create([
            'balance' => 0,  // Initial balance
            'reserved_amount' => 0, // Initial reserved amount
        ]);
    }

    // Other event methods (update, delete, etc.) can be added here if needed
}
