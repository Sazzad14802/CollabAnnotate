<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDatasetJob;
use App\Models\Dataset;
use App\Services\ActivityLogService;
use App\Services\DatasetImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DatasetController extends Controller
{
    public function index(): View
    {
        $datasets = auth()->user()->datasets()
            ->latest()
            ->paginate(15);

        return view('datasets.index', compact('datasets'));
    }

    public function create(): View
    {
        return view('datasets.create');
    }

    public function store(Request $request, DatasetImportService $importService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:csv,xlsx', 'max:51200'],
        ]);

        $user    = auth()->user();
        $dataset = $importService->prepareDataset($request->file('file'), $request->name, $user->id);

        ActivityLogService::log($user, 'dataset.uploaded', "Dataset \"{$dataset->name}\" uploaded.");

        ImportDatasetJob::dispatch($dataset, $user);

        return redirect()->route('datasets.show', $dataset)
            ->with('success', 'Dataset uploaded. Import is processing in the background.');
    }

    public function show(Dataset $dataset): View
    {
        $this->authorize('view', $dataset);
        // $dataset->load('projects');

        $rows = $dataset->rows()->paginate(25);

        return view('datasets.show', compact('dataset', 'rows'));
    }

    public function destroy(Dataset $dataset): RedirectResponse
    {
        $this->authorize('delete', $dataset);

        ActivityLogService::log(auth()->user(), 'dataset.deleted',
            "Dataset \"{$dataset->name}\" deleted.");

        $dataset->delete();

        return redirect()->route('datasets.index')->with('success', 'Dataset deleted.');
    }

    // Export the dataset (called from ExportController)
    public function edit(Dataset $dataset): View
    {
        $this->authorize('update', $dataset);
        return view('datasets.edit', compact('dataset'));
    }

    public function update(Request $request, Dataset $dataset): RedirectResponse
    {
        $this->authorize('update', $dataset);

        $request->validate(['name' => ['required', 'string', 'max:255']]);
        $dataset->update(['name' => $request->name]);

        return back()->with('success', 'Dataset updated.');
    }
}
