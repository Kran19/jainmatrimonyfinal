<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileRejectedNotification extends Notification
{
    use Queueable;

    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
    }

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
            ->subject('Profile Rejected – Action Required')
            ->greeting("Dear {$notifiable->full_name},")
            ->line('Your Matrimony profile has been rejected for the following reason:')
            ->line("**{$this->rejectionReason}**")
            ->line('Please log in to your account, update the required information, and submit your profile again for approval.')
            ->action('Log In & Update Profile', route('login'))
            ->line('Your profile, photos, and Profile ID will remain hidden until your profile is approved.')
            ->line('Thank you.');
    }
}
