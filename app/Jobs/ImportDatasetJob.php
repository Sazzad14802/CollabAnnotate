<?php

namespace App\Jobs;

use App\Models\Dataset;
use App\Models\User;
use App\Services\DatasetImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportDatasetJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $timeout = 600; // 10 minutes for large files
    public int $tries   = 3;

    public function __construct(
        public readonly Dataset $dataset,
        public readonly User    $user
    ) {}

    public function handle(DatasetImportService $service): void
    {
        $service->importRows($this->dataset);

    }

    public function failed(\Throwable $e): void
    {
        $this->dataset->update([
            'import_status' => 'failed',
            'import_error'  => $e->getMessage(),
        ]);
    }
}
