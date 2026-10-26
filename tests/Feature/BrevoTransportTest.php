<?php

use App\Mail\BrevoTransport;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

test('brevo transport sends payload with attachments', function () {
    Http::fake([
        'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => 'test-id'], 200),
    ]);

    $transport = new BrevoTransport('test-api-key');

    $email = (new Email())
        ->from(new Address('sender@example.com', 'Sender Name'))
        ->to(new Address('recipient@example.com'))
        ->subject('Test Subject')
        ->html('<p>Hello World</p>')
        ->addPart(new DataPart('PDF-CONTENT', 'receipt.pdf', 'application/pdf'));

    $transport->send($email);

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $request->url() === 'https://api.brevo.com/v3/smtp/email'
            && $request->header('api-key')[0] === 'test-api-key'
            && $data['subject'] === 'Test Subject'
            && isset($data['attachment'])
            && count($data['attachment']) === 1
            && $data['attachment'][0]['name'] === 'receipt.pdf'
            && $data['attachment'][0]['content'] === base64_encode('PDF-CONTENT');
    });
});
