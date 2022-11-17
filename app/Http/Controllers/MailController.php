<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\MailBill;
use App\Mail\MailFeedback;
use App\Mail\MailReserv;
use App\Mail\MailLeave;

class MailController extends Controller
{
    //
    public function send_mail_bill($email_to,$file)
    {
        Mail::to($email_to)->send(new MailBill(['file' => $file]));
    }

    public function send_mail_feedback($email_to, $data)
    {
        Mail::to($email_to)->send(new MailFeedback($data));
    }

    public function send_mail_reserv($email_to, $data)
    {
        Mail::to($email_to)->send(new MailReserv($data));
    }

    public function send_mail_leave($email_to, $data)
    {
        Mail::to($email_to)->send(new MailLeave($data));
    }

}
