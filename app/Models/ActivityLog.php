<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'url',
        'client_visible',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $description, ?string $url = null, ?int $projectId = null,bool $clientVisible = false): void
    {
        $log = static::create([
            'user_id'     => auth()->id(),
            'description' => $description,
            'url'         => $url,
            'is_read'     => false,
            'client_visible' => $clientVisible,
            'project_id' => $projectId,
        ]);
        if (auth()->check()) {
            static::markReadFor(auth()->user(), [$log->id]);
        }
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'activity_log_reads')->withPivot('read_at');
    }

    public function scopeUnreadFor($query, User $user)
    {
        return $query->whereDoesntHave('readers', fn ($q) => $q->where('users.id', $user->id));
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'admin') {
            return $query;
        }

        return $query->where('client_visible', true)
            ->whereHas('project', fn ($p) => $p->where('client_id', $user->id));
    }

    public static function markReadFor(User $user, $logIds): void
    {
        $rows = collect($logIds)->map(fn ($id) => [
            'activity_log_id' => $id,
            'user_id'         => $user->id,
            'read_at'         => now(),
        ])->all();

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('activity_log_reads')->insertOrIgnore($chunk);
        }
    }

    public static function markAllReadFor(User $user): void
    {
        static::markReadFor($user, static::visibleTo($user)->unreadFor($user)->pluck('id'));
    }

    public function urlFor(User $user): ?string
    {
        // Admins open the stored link as is
        if ($user->role === 'admin') {
            return $this->url;
        }

        // Clients open it only if it's not an admin page
        $path = $this->url ? (parse_url($this->url, PHP_URL_PATH) ?? '') : '';

        if ($this->url && ! str_starts_with($path, '/admin')) {
            return $this->url;
        }

        // Otherwise send them to their project page
        return $this->project_id
            ? route('dashboard.project', $this->project_id)
            : route('client.activity.index');
    }
}