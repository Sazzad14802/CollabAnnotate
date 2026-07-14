<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 1800; // 30 minutes

    public function __construct(
        public readonly Project $project,
        public readonly User    $user,
    ) {}

    public function handle(ProjectImportService $importService): void
    {
        $importService->importRows($this->project);
    }

    public function failed(\Throwable $exception): void
    {
        $this->project->update([
            'import_status' => 'failed',
            'import_error'  => $exception->getMessage(),
        ]);
    }
}
