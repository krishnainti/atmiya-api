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
        $name = $this->profile->first_name.' ' .$this->profile->first_name;
        $membership_category = $this->profile->membershipCategory->name;
        $payment_type = $this->profile->payment_mode;
        $zelle_payment_email= env('ZELLE_PAYMENT_EMAIL');


        if($payment_type=='zelle') {
            $message = 'We thank you for your interest to be a part of ATMIYA Core Mission to educate, empower, enrich and elevate community members through financial education and enterprenurial networking opportunities. We have received your application for '.$membership_category.'.
            Please use the email '.  $zelle_payment_email  .' to pay membership fee. Our membership team will review your application as soon as the receipt of membership fee payment and we will inform you once the review process is complete.';
        }else {
            $message = 'We thank you for your interest to be a part of ATMIYA Core Mission to educate, empower, enrich and elevate community members through financial education and enterprenurial networking opportunities.
            We have received your application for '.$membership_category. ' Our membership team is reviewing your application and we will inform you once the review process is complete.';
        }
   
       // return $this->markdown('vendor.mail.text.message', ['header'=>$footer, 'subcopy' =>$footer ,'slot' => $message]);
        return $this->markdown('mails.postRegistrationNotification', ['name'=>$name, 'message'=>$message ]);
    }
}
