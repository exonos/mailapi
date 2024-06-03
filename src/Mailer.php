<?php

namespace Exonos\Mailapi;

use Exonos\Mailapi\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailer = $this->from(config('mail.from.address'), $this->mail->batch->from)
            ->replyTo(config('mail.from.address'), $this->mail->batch->from)
            ->subject($this->mail->subject);
        if ($this->mail->html) {
            $mailer->html($this->mail->html);
        } else {
            $mailer->text($this->mail->text);
        }

        if ($this->mail->batch->attachments) {
            foreach ($this->mail->batch->attachments as $attachment) {
                $mailer->attachData(base64_decode($attachment['content']), $attachment['filename']);
            }
        }
        return $mailer;
    }
}