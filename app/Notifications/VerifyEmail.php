<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address - ' . config('app.name'))
            ->view('emails.verification', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        $url = parent::verificationUrl($notifiable);
        
        // Parse the backend URL
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? '';
        
        // Ensure path starts with /api/
        if (!str_starts_with($path, '/api/')) {
            $path = '/api' . $path;
        }
        
        // Build frontend verification URL
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://127.0.0.1:5174'));
        
        return $frontendUrl . '/verify-email?' . $query . '&path=' . urlencode($path);
    }
}
