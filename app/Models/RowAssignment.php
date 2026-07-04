<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RowAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'dataset_row_id',
        'user_id',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function datasetRow(): BelongsTo
    {
        return $this->belongsTo(DatasetRow::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
