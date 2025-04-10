<?php

namespace App\Notifications;

use App\Models\Campaign;
use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CampaignFinalizedNotification extends Notification
{
    use Queueable;

    public $campaign;
    public $investment;

    /**
     * Create a new notification instance.
     * This constructor receives the campaign and optionally an investment instance.
     *
     * @param  Campaign  $campaign
     * @param  Investment|null  $investment
     * @return void
     */
    public function __construct(Campaign $campaign, Investment $investment = null)
    {
        $this->campaign = $campaign;
        $this->investment = $investment;
    }

    /**
     * Get the notification's mail representation.
     * Defines the structure and content of the email notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
                    ->line('The campaign "' . $this->campaign->name . '" has been finalized.');

        if ($this->investment) {
            // Details for the specific investment
            $message->line('Your investment of ' . $this->investment->amount . '€ has been processed.');
            if ($this->investment->status == 'accepted') {
                $message->line('Your investment has been accepted in the campaign.');
            } else {
                $message->line('Your investment has been rejected in the campaign.');
            }
        }

        // Additional information for the campaign owner
        if ($this->investment === null) {
            $message->line('The campaign has been successfully finalized.');
            $message->line('Total amount funded: ' . ($this->campaign->amount + $this->campaign->contract_fee) . '€.');
            $message->line('Details of the loan will be sent soon.');
        }

        $message->line('Thank you for your participation.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     * This is the data sent to other channels (like the database).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'campaign_id' => $this->campaign->id,
            'message' => 'The campaign "' . $this->campaign->name . '" has been finalized.',
        ];
    }
}
