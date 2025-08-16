<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AllPlatesChallanAttachMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attachments;

    public function __construct($attachments)
    {
        $this->attachments = $attachments;
    }

    public function build()
    {
        $mail = $this->subject('All Plates and Challans Attached')
                     ->view('emails.all_plates_challan_attach');

        foreach ($this->attachments as $file) {
            $mail->attach($file);
        }

        return $mail;
    }
}
