<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('forgot password answers the same for known and unknown emails', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'known@example.com']);

    $known = $this->post(route('password.email'), ['email' => 'known@example.com']);
    $unknown = $this->post(route('password.email'), ['email' => 'nobody@example.com']);

    $known->assertSessionHas('success')->assertSessionHasNoErrors();
    $unknown->assertSessionHas('success')->assertSessionHasNoErrors();

    // only the real account got an email
    Notification::assertSentTo($user, ResetPassword::class);
    Notification::assertCount(1);
});