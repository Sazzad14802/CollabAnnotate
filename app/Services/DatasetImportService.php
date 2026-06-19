<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DatasetRow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class DatasetImportService
{
    private const BULK_INSERT_CHUNK = 500;

    /**
     * Store the uploaded file and create the Dataset record.
     * Returns the Dataset; the actual row import is dispatched as a job.
     */
    public function prepareDataset(UploadedFile $file, string $name, int $userId): Dataset
    {
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->store('datasets', 'local');

        // Peek at headers only (fast)
        $headers = $this->extractHeaders($file->getRealPath(), $ext);

        return Dataset::create([
            'user_id'           => $userId,
            'name'              => $name,
            'original_filename' => $file->getClientOriginalName(),
            'column_names'      => $headers,
            'file_path'         => $path,
            'import_status'     => 'pending',
        ]);
    }

    /**
     * Full import: read all rows and bulk-insert into dataset_rows.
     * Called from ImportDatasetJob.
     */
    public function importRows(Dataset $dataset): void
    {
        $fullPath = storage_path('app/private/' . $dataset->file_path);
        $ext      = pathinfo($dataset->original_filename, PATHINFO_EXTENSION);

        $dataset->update(['import_status' => 'processing']);

        try {
            $rows = $this->readAllRows($fullPath, strtolower($ext), $dataset->column_names);
            $totalInserted = 0;

            DB::transaction(function () use ($dataset, $rows, &$totalInserted) {
                $now   = now();
                $chunk = [];

                foreach ($rows as $index => $rowData) {
                    $chunk[] = [
                        'dataset_id' => $dataset->id,
                        'row_index'  => $index,
                        'data'       => json_encode($rowData),
                        'status'     => 'unassigned',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($chunk) >= self::BULK_INSERT_CHUNK) {
                        DatasetRow::insert($chunk);
                        $totalInserted += count($chunk);
                        $chunk = [];
                    }
                }

                if (!empty($chunk)) {
                    DatasetRow::insert($chunk);
                    $totalInserted += count($chunk);
                }
            });

            $dataset->update([
                'row_count'     => $totalInserted,
                'import_status' => 'completed',
            ]);

        } catch (\Throwable $e) {
            $dataset->update([
                'import_status' => 'failed',
                'import_error'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function extractHeaders(string $path, string $ext): array
    {
        if ($ext === 'csv') {
            $handle = fopen($path, 'r');
            $headers = fgetcsv($handle);
            fclose($handle);
            return array_map('trim', $headers ?: []);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $v = trim((string) $cell->getValue());
                if ($v !== '') $headers[] = $v;
            }
        }
        return $headers;
    }

    private function readAllRows(string $path, string $ext, array $headers): \Generator
    {
        if ($ext === 'csv') {
            $handle = fopen($path, 'r');
            fgetcsv($handle); // skip header row
            while (($row = fgetcsv($handle)) !== false) {
                if (array_filter($row, fn($v) => $v !== null && $v !== '')) {
                    yield array_combine($headers, array_pad($row, count($headers), null));
                }
            }
            fclose($handle);
            return;
        }

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $firstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            if ($firstRow) { $firstRow = false; continue; }
            $values = [];
            foreach ($row->getCellIterator() as $cell) {
                $values[] = $cell->getFormattedValue();
            }
            if (array_filter($values, fn($v) => $v !== '')) {
                yield array_combine($headers, array_pad($values, count($headers), null));
            }
        }
    }
}
