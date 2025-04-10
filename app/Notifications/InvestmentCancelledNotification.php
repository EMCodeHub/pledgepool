<?php

namespace App\Notifications;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvestmentCancelledNotification extends Notification
{
    use Queueable;

    public $investment;

    /**
     * Create a new notification instance.
     *
     * @param  Investment  $investment
     * @return void
     */
    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    /**
     * Determine which channels the notification will be sent on.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // You can add other channels if needed, like 'database'
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
        return (new MailMessage)
            ->subject('Investment Cancelled') // Subject of the email
            ->greeting('Hello ' . $notifiable->name . ',') // Personalized greeting
            ->line('Your investment of ' . $this->investment->amount . '€ in the campaign "' . $this->investment->campaign->name . '" has been cancelled.') // Main message body
            ->line('The amount has been released and is now available in your investment account.') // Additional information
            ->line('Thank you for your understanding.'); // Closing line
    }

    /**
     * Get the array representation of the notification.
     * This is used for other channels, like saving to the database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'investment_id' => $this->investment->id, // Include the investment ID
            'message' => 'Your investment has been cancelled.', // Short message for array representation
        ];
    }
}
