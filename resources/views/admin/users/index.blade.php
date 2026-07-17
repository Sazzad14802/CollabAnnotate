<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 fw-semibold">🛡 Admin Panel</h2>
    </x-slot>

    <div class="container-fluid py-4 px-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">User Management</h4>
                <p class="text-muted small mb-0">All registered accounts in the system.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex" style="width: 250px;">
                    <input type="text" name="email" class="form-control form-control-sm me-2" placeholder="Search by email..." value="{{ request('email') }}">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </form>
                <span class="badge bg-secondary">{{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }}</span>
            </div>
        </div>



        @if($users->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <svg class="mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h6 class="fw-semibold mb-0">No users in the system</h6>
                </div>
            </div>
        @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">Owned Projects</th>
                                <th class="text-center">Assigned To</th>
                                <th>Joined</th>
                                <th class="text-center" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $i => $user)
                                <tr>
                                    <td class="text-muted small font-monospace">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                                 style="width:34px;height:34px;font-size:13px;flex-shrink:0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $user->email }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                            {{ $user->owned_projects_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                            {{ $user->assigned_projects_count }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Delete {{ $user->name }}? This will permanently remove their account, all projects, annotations and assignments.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
