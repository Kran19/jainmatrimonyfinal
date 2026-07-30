<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $candidate;
    protected $transactionId;
    protected $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct($candidate, $transactionId, $amount)
    {
        $this->candidate = $candidate;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
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
            ->subject('New Subscription Payment Receipt Submitted')
            ->greeting("Hello Admin {$notifiable->name},")
            ->line("A candidate has uploaded a premium subscription receipt for review.")
            ->line("Candidate Name: **{$this->candidate->full_name}**")
            ->line("Amount Paid: **₹" . number_format($this->amount, 2) . "**")
            ->line("Transaction Reference: **{$this->transactionId}**")
            ->action('Verify Transaction', route('admin.payments.index'))
            ->line('Please audit this receipt inside your verification dashboard.');
    }
}
