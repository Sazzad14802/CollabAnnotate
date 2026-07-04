<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\AnnotationExportService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function download(Project $project, string $format, AnnotationExportService $exportService): BinaryFileResponse
    {
        $this->authorize('export', $project);

        if (!in_array($format, ['csv', 'xlsx'])) {
            abort(400, 'Invalid export format.');
        }

        $tempFile = $exportService->export($project, $format);

        $filename = str($project->name)->slug() . '_annotations_' . now()->format('Ymd_His') . '.' . $format;

        ActivityLogService::log(auth()->user(), 'dataset.exported',
            "Dataset exported as {$format} for project \"{$project->name}\".", $project);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
