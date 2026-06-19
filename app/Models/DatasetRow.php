<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatasetRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'row_index',
        'data',
        'assigned_to',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'row_index' => 'integer',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Annotation relations will be added in Commit 5

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
