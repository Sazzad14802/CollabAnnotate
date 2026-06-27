<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dataset_id',
        'name',
        'description',
        'status',
        'chunk_size',
    ];

    protected $casts = [
        'chunk_size' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }


    // Relations for Commit 5 & 6 commented out for now
    /*
    public function annotationFields(): HasMany
    {
        return $this->hasMany(AnnotationField::class)->orderBy('order');
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(Annotation::class);
    }
    */

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->withPivot('role', 'joined_at')
            ->withTimestamps()
            ->withCasts(['joined_at' => 'datetime']);
    }

    public function annotators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->wherePivot('role', 'annotator')
            ->withPivot('role', 'joined_at')
            ->withTimestamps()
            ->withCasts(['joined_at' => 'datetime']);
    }

    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    // Progress helpers
    public function totalRows(): int
    {
        return $this->dataset->row_count;
    }

    public function completedRows(): int
    {
        return $this->dataset->rows()->where('status', 'completed')->count();
    }

    public function progressPercentage(): float
    {
        $total = $this->totalRows();
        if ($total === 0) return 0;
        return round(($this->completedRows() / $total) * 100, 1);
    }
}
