<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateMailSender extends Mailable
{
    use Queueable, SerializesModels;
    protected $to_mail;
    protected $mail_data;
    protected $mail_view;
    private $_bcc;

    /**
     * Create a new message instance.
     *
     * @param $mail_to
     * @param array $mail_data
     */

    public function __construct($mail_to,$mail_data=[])
    {
        $this->to_mail = $mail_to;
        $this->mail_data = $mail_data;
        $this->mail_view = "mailtest";
        $this->_bcc = [];
    }

    public function setView($view) {
        $this->mail_view = $view;
    }

    public function addBcc($bcc) {
        $this->_bcc[] = $bcc;
    }

    public function addSubject($subject) {
        $this->subject = $subject;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->to_mail)
            ->view($this->mail_view)
            ->subject($this->subject)
            ->from("info@admin.bitch.team", 'Bitch Intelligence Team')
            ->with($this->mail_data)
            ->bcc($this->_bcc);

    }

}
