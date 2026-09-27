<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;
     public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
    protected $fillable = [
        'milestones',
        'id',
        'project_id',
        'title',
        'description',
        'status',
        'due_date',
    'client_notes',
    'approved_at',
        'completed_at',
        'created_at'
    ];
    protected $casts = [
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    public function project()
{
    return $this->belongsTo(Project::class);
}
}
