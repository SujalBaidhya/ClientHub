<?php

namespace App\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SafeMail
{
    /**
     * Send an email without ever breaking the request.
     * Returns true if sent, false if it failed (the error is logged).
     */
    public static function send($to, Mailable $mailable): bool
    {
        try {
            Mail::to($to)->send($mailable);

            return true;
        } catch (\Throwable $e) {
            Log::error('Email failed: ' . get_class($mailable) . ' - ' . $e->getMessage());

            return false;
        }
    }
}