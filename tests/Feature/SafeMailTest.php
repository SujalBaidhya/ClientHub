<?php

use App\Support\SafeMail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

test('SafeMail returns false instead of throwing when sending fails', function () {
    Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Brevo is down'));

    $result = SafeMail::send('someone@example.com', new Mailable());

    expect($result)->toBeFalse();
});

test('SafeMail returns true when the email is sent', function () {
    Mail::fake();

    $result = SafeMail::send('someone@example.com', new Mailable());

    expect($result)->toBeTrue();
});