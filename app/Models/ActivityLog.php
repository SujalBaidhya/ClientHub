<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'url',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $description, ?string $url = null, ?int $projectId = null): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'description' => $description,
            'url'         => $url,
            'is_read'     => false,
            'project_id' => $projectId,
        ]);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}