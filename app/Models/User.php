<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'invitation_token',
'invitation_sent_at',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'invitation_sent_at' => 'datetime',
            'password' => 'hashed',
                        'email_invoices' => 'boolean',
            'email_reminders' => 'boolean',
            'email_milestones' => 'boolean',
        ];
    }
        /**
     * Whether this user wants an optional email. Categories: invoices, reminders, milestones.
     * Essential emails (invitation, password reset, payment receipt) never check this.
     */
    public function wantsEmail(string $category): bool
    {
        $column = 'email_' . $category;

        return (bool) ($this->{$column} ?? true);
    }
    public function projects()
    {
        return $this->hasMany(\App\Models\Project::class, 'client_id');
    }
}
