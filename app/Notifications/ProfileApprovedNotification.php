<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations! Your Matrimony Profile is Verified')
            ->greeting("Hello {$notifiable->full_name},")
            ->line('We are pleased to inform you that your matrimonial profile has been reviewed and verified by our committee.')
            ->line("Your assigned unique Profile ID is: **{$notifiable->profile_id}**")
            ->action('Browse Profiles', route('login'))
            ->line('You can now log in to view match recommendations and connect with other candidates.')
            ->line('Thank you for registering with Jain Digambar Matrimony.');
    }
}
