<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CampaignClosed extends Notification
{
    use Queueable;

    /**
     * Get the channels the notification should be delivered on.
     * In this case, the notification will be sent via email.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // You can add more channels if needed (e.g., database, SMS)
    }

    /**
     * Get the mail representation of the notification.
     * Defines the email content, including the subject and the message body.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Campaign Closed')  // The subject of the email
                    ->line('Your campaign has been successfully closed.');  // The message body of the email
    }
}
