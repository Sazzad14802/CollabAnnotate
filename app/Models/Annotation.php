<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Annotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'dataset_row_id',
        'user_id',
        'annotation_field_id',
        'value',
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

    public function field(): BelongsTo
    {
        return $this->belongsTo(AnnotationField::class, 'annotation_field_id');
    }
}
