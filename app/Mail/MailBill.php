<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailBill extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data = [];
     
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file = \Storage::disk('public')->get($this->data['file']);
        $date = date('Y-m-d');
        return $this->subject("Bill {$date}")
                ->attachData($file, "Bill {$date}.pdf")
                ->view('emails.bill');
    }

  
}
