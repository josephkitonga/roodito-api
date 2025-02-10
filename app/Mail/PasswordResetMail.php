<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $user_id;

    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $user_id
     * @return void
     */
    public function __construct($name, $user_id)
    {
        $this->name = $name;
        $this->user_id = $user_id;
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
                        'user_id' => $this->user_id
                    ]);
    }
}
