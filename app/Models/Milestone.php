<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'due_date',
        'client_notes',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
        /** Days until the due date (negative = late). Null when no due date. */
    public function daysUntilDue(): ?int
    {
        return $this->due_date
            ? (int) now()->startOfDay()->diffInDays($this->due_date->copy()->startOfDay(), false)
            : null;
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->daysUntilDue() !== null && $this->daysUntilDue() < 0;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}