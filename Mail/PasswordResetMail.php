<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $remember_token;

    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $user_id
     * @return void
     */
    public function __construct($name, $remember_token)
    {
        $this->name = $name;
        $this->remember_token = $remember_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Password Reset Request - Roodito')
                    ->view('emails.password-reset')
                    ->with([
                        'name' => $this->name,
                        'remember_token' => $this->remember_token
                    ]);
    }
}
