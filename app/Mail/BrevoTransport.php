<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    protected string $key;

    public function __construct(string $key)
    {
        parent::__construct();
        $this->key = $key;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $sender = $email->getFrom()[0] ?? null;
        $senderEmail = $sender ? $sender->getAddress() : config('mail.from.address');
        $senderName = $sender ? ($sender->getName() ?: config('mail.from.name')) : config('mail.from.name');

        $toRecipients = [];
        foreach ($email->getTo() as $recipient) {
            $toRecipients[] = [
                'email' => $recipient->getAddress(),
                'name'  => $recipient->getName() ?: null,
            ];
        }

        $payload = [
            'sender' => [
                'name'  => $senderName,
                'email' => $senderEmail,
            ],
            'to'          => $toRecipients,
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: nl2br($email->getTextBody() ?? ''),
        ];

        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'name'    => $attachment->getName(),
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        if (!empty($attachments)) {
            $payload['attachment'] = $attachments;
        }

        $response = Http::withHeaders([
            'api-key'      => $this->key,
            'accept'       => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Brevo API send error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
