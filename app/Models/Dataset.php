<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'original_filename',
        'column_names',
        'row_count',
        'file_path',
        'import_status',
        'import_error',
    ];

    protected $casts = [
        'column_names' => 'array',
        'row_count' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(DatasetRow::class);
    }



    public function isImported(): bool
    {
        return $this->import_status === 'completed';
    }
}
