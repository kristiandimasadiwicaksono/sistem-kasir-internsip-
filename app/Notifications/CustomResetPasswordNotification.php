<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔐 Reset Password Akun Sistem Kasir')
            ->greeting('Halo, ' . $notifiable->name . ' 👋')
            ->line('Kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password Sekarang', $url)
            ->line('Jika Anda tidak merasa meminta reset password, abaikan email ini.')
            ->salutation('Salam hangat, Tim Sistem Kasir 💙');
    }
}
