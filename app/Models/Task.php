<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'progress_percent',
        'priority',
        'estimated_minutes',
        'started_at',
        'due_at',
        'completed_at',
        'created_by',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'started_at' => 'datetime',
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Task $task): void {
            $task->progress_percent = max(0, min(100, (int) $task->progress_percent));
            $task->started_at ??= now();

            if ($task->progress_percent >= 100) {
                $task->progress_percent = 100;
                $task->status = TaskStatus::Completed;
            }

            if ($task->status === TaskStatus::Completed) {
                $task->progress_percent = 100;
                $task->completed_at ??= now();

                return;
            }

            $task->completed_at = null;
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeForStatus(Builder $query, TaskStatus|string|null $status): Builder
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status instanceof TaskStatus ? $status->value : $status);
    }

    public function scopeForPriority(Builder $query, TaskPriority|string|null $priority): Builder
    {
        if (! $priority) {
            return $query;
        }

        return $query->where('priority', $priority instanceof TaskPriority ? $priority->value : $priority);
    }

    public function scopeAssignedTo(Builder $query, ?int $userId): Builder
    {
        if (! $userId) {
            return $query;
        }

        return $query->where('assigned_to', $userId);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->where('status', '!=', TaskStatus::Completed->value);
    }

    public function isOverdue(): bool
    {
        return $this->due_at instanceof Carbon
            && $this->due_at->isPast()
            && $this->status !== TaskStatus::Completed;
    }

    public function remainingSeconds(): int
    {
        $startedAt = $this->started_at ?? $this->created_at;

        if (! $startedAt instanceof Carbon) {
            return 0;
        }

        $endsAt = $startedAt->copy()->addMinutes($this->estimated_minutes);
        $reference = $this->completed_at ?? now();

        return max(0, $reference->diffInSeconds($endsAt, false));
    }
}
