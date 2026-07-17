<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Email Z-Airlines')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Terima kasih telah mendaftar di Z-Airlines.')
            ->line('Klik tombol di bawah untuk memverifikasi alamat email sebelum menggunakan fitur booking dan pembayaran.')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Tautan ini akan kedaluwarsa sesuai pengaturan keamanan aplikasi.')
            ->salutation('Salam, Tim Z-Airlines');
    }
}
