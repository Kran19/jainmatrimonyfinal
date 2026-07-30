<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileLikedNotification extends Notification
{
    use Queueable;

    protected $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct($liker)
    {
        $this->liker = $liker;
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
            ->subject('A Member has Shortlisted Your Profile!')
            ->greeting("Hello {$notifiable->full_name},")
            ->line("An approved candidate has shortlisted your matrimonial profile on Jain Digambar Matrimony.")
            ->line("Shortlisted by: **{$this->liker->profile_id}**")
            ->action('View My Dashboard', route('login'))
            ->line('Log in to view matches and explore mutual compatibility.');
    }
}
