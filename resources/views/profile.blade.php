<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">Profile</h2>
    </x-slot>

    <div class="container py-4" style="max-width:700px;">
        <div class="card mb-4">
            <div class="card-body">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
