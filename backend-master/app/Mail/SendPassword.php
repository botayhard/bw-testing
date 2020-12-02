<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $password;

    /**
     * Create a new message instance.
     *
     * @param $password
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@admin.bitch.team', 'Bitch Intelligence Team')
                ->subject('Пароль от аккаунта на admin.bitch.team')
                ->view('sendPassword')
                ->with(['password' => $this->password]);
    }
}
