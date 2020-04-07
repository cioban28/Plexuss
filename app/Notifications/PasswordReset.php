<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReset extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Plexuss Password Reset')
            ->line('You are receiving this email because there has been a request to reset your password to your account.')
            ->line('You may also have received this email because you have created an account on Plexuss to communicate with universities and scholarship programs. In order to complete your signup, click here to create a secure password.')
            ->action('Reset Password', 'https://plexuss.com/resetpassword/' . $this->token)
            ->line('If you were not supposed to receive a password reset, no further action is required.')
            ->line('Regards,')
            ->salutation('Plexuss Team');
    }
}
