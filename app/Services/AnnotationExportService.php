<?php

namespace App\Services;

use App\Models\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AnnotationExportService
{
    public function export(Project $project, string $format = 'csv'): string
    {
        $fields = $project->annotationFields()->orderBy('order')->get();
        $rows   = $project->rows()->with('annotations.field')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Annotations');

        // Build headers
        $dataColumns       = $project->column_names ?? [];
        $annotationColumns = $fields->pluck('name')->toArray();
        $headers           = array_merge($dataColumns, $annotationColumns);

        // Write header row
        foreach ($headers as $col => $header) {
            $coordinate = Coordinate::stringFromColumnIndex($col + 1) . '1';
            $cell = $sheet->getCell($coordinate);
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
        }

        // Write data rows
        $rowNum = 2;
        foreach ($rows as $row) {
            $col = 1;

            // Original data columns
            foreach ($dataColumns as $colName) {
                $coordinate = Coordinate::stringFromColumnIndex($col) . $rowNum;
                $sheet->getCell($coordinate)->setValue($row->data[$colName] ?? '');
                $col++;
            }

            // Annotation columns — pick first annotation per field
            foreach ($fields as $field) {
                $annotation = $row->annotations
                    ->where('annotation_field_id', $field->id)
                    ->first();
                $coordinate = Coordinate::stringFromColumnIndex($col) . $rowNum;
                $sheet->getCell($coordinate)->setValue($annotation?->value ?? '');
                $col++;
            }

            $rowNum++;
        }

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'export_') . '.' . $format;

        if ($format === 'xlsx') {
            $writer = new XlsxWriter($spreadsheet);
        } else {
            $writer = new CsvWriter($spreadsheet);
        }

        $writer->save($tempFile);
        return $tempFile;
    }
}
