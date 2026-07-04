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
    ];

    protected $casts = [
        'data' => 'array',
        'row_index' => 'integer',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function rowAssignments(): HasMany
    {
        return $this->hasMany(RowAssignment::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(Annotation::class);
    }
}
