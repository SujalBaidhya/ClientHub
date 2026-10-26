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
        public string $activityType, // 'comment', 'revision' or 'approval'
        public string $activityMessage,
    ) {
    }

    public function build()
    {
        $subject = match ($this->activityType) {
            'revision' => "Revision requested: {$this->milestone->title}",
            'approval' => "Milestone approved: {$this->milestone->title}",
            default => "New comment from {$this->client->name}",
        };

        return $this->subject($subject)
            ->view('emails.client-activity');
    }
}