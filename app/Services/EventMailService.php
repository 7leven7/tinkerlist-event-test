<?php

namespace App\Services;

use Mailgun\Mailgun;

class EventMailService
{
    protected $mailgun;

    public function __construct()
    {
        $this->mailgun = Mailgun::create(config('mail.mailers.mailgun.secret'));
    }

    public function sendEmail($from, $to, $subject, $body)
    {
        $this->mailgun->messages()->send(config('mail.mailers.mailgun.domain'), [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $body,
        ]);
    }

    public function sendInvitationEmail(array $inviteeData, string $title, string $dateTime, string $locationName)
    {
        $from = auth()->user()->email;
        $to = $inviteeData['email'];
        $subject = 'Invitation for ' . $title;

        $data = [
            'name' => $inviteeData['name'],
            'event_date' => $dateTime,
            'event_name' => $title,
            'from_name' => auth()->user()->name,
            'location' => $locationName
        ];

        $view = view('emails.event')->with($data)->render();

        $this->sendEmail($from, $to, $subject, $view);
    }
}
