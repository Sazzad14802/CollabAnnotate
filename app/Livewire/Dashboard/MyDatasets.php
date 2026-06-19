<?php

namespace App\Livewire\Dashboard;

use App\Models\Dataset;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class MyDatasets extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteDataset(int $id): void
    {
        $dataset = Dataset::findOrFail($id);
        $this->authorize('delete', $dataset);

        $dataset->delete();
        $this->dispatch('dataset-deleted');
    }

    public function render(): View
    {
        $datasets = auth()->user()->datasets()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(8);

        return view('livewire.dashboard.my-datasets', compact('datasets'));
    }
}
