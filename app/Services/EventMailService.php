<?php

namespace App\Services;

use Mailgun\Mailgun;
use Psr\Http\Client\ClientExceptionInterface;

class EventMailService
{
    /**
     * @var Mailgun
     */
    protected $mailgun;

    public function __construct()
    {
        $this->mailgun = Mailgun::create(config('mail.mailers.mailgun.secret'));
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $body
     * @return void
     * @throws ClientExceptionInterface
     */
    public function sendEmail($from, $to, $subject, $body): void
    {
        $this->mailgun->messages()->send(config('mail.mailers.mailgun.domain'), [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $body,
        ]);
    }

    /**
     * @param array $inviteeData
     * @param string $title
     * @param string $dateTime
     * @param string $locationName
     * @return void
     * @throws ClientExceptionInterface
     */
    public function sendInvitationEmail(array $inviteeData, string $title, string $dateTime, string $locationName): void
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
