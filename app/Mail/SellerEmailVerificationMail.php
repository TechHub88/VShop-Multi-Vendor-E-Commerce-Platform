<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $sellerName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp, $sellerName)
    {
        $this->otp = $otp;
        $this->sellerName = $sellerName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('VSHOP Seller - Profile Email Verification OTP')
                    ->view('emails.seller_email_otp');
    }
}
