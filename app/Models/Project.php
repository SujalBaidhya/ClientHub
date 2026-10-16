<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            foreach ($project->files as $file) {
                Storage::disk('public')->delete($file->file_path);
            }
        });
    }
    protected $fillable = [
        'id',
        'client_id',
        'name',
        'description',
        'status',
        'start_date',
        'due_date',
                'archived_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
                'archived_at' => 'datetime',
    ];
        public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function milestones()
{
    return $this->hasMany(Milestone::class);
}
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
