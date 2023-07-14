<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $profile;

    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Notification',
        );
    }

    public function build()
    {
        $name = $this->profile->first_name.' ' .$this->profile->last_name;
        $membership_category = $this->profile->membershipCategory->name;
        $payment_type = $this->profile->payment_mode;
        $zelle_payment_email= env('ZELLE_PAYMENT_EMAIL');

        return $this->markdown('mails.postRegistrationNotification', ['name'=> $name, "payment_type"=>$payment_type, "membership_category" => $membership_category, 'zelle_payment_email'=> $zelle_payment_email ]);

    }
}
