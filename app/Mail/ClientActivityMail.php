<?php

namespace App\Mail;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientActivityMail extends Mailable
{
    use Queueable, SerializesModels;

        public function __construct(
        public User $client,
        public Milestone $milestone,
        public string $activityType, // 'comment' or 'revision'
        public string $activityMessage,
    ) {
    }

    public function build()
    {
        $subject = $this->activityType === 'revision'
            ? "Revision requested: {$this->milestone->title}"
            : "New comment from {$this->client->name}";

        return $this->subject($subject)
            ->view('emails.client-activity');
    }
}