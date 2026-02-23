<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResetPasswordMail extends Mailable implements ShouldQueue {
    use Queueable, SerializesModels;

    public  $user;
    public $token;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $token) {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Summary of build
     * @return ResetPasswordMail
     */
    public function build() {
        try {
                Log::info('Building reset password mail for user: ' . $this->user->email . ' with token: ' . $this->token);
            $frontendUrl = 'http://localhost:3000';
            $resetUrl = $frontendUrl . '/reset-password?token=' . $this->token .'/'.'&email=' . urlencode($this->user->email);
          
            return $this->subject('Reset Your Password - ' . config('app.name'))
                ->view('emails.Auth.reset-password')
                ->with([
                    'user' => $this->user,
                    'token' => $this->token,
                    'resetUrl' => $resetUrl,
                ]);
        } catch (Exception $e) {
            Log::error("Fail To Reset Password .." . $e->getMessage());
            throw $e;
        }
    }
}
