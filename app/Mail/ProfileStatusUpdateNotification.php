<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileStatusUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $status;
    private $profile;

    public function __construct($status, $profile)
    {
        $this->status = $status;
        $this->profile = $profile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Profile Status Update Notification',
        );
    }


    public function build()
    {
        $name = $this->profile->first_name. ' ' . $this->profile->last_name;
        $message='';
        if($this->status == 'admin_rejected') {
            $message = 'We thank you for your membership application to be part of ATMIYA mission to educate, empower, enrich and elevate community members.
            Unfortunately, we cannot approve your application for membership at this time.
            Please contact info@atmiyausa.org for any further information.';
        }else if ($this->status == 'admin_approved'){
            $message =  'CONGRATULATIONS and welcome to ATMIYA. Your membership application has been reviewed and approved. You can now log in to atmiyausa.org to view and edit your profile and access additional content including recorded sessions of IT Training, BEST and FIRE sessions etc.';

        }
        return $this->markdown('mails.statusNotification', ['message'=>$message , 'name'=>$name]);
    }
}
