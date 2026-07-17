<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::where('is_admin', false)
            ->withCount([
                'ownedProjects',
                'assignedProjects',
            ]);

        if (request()->filled('email')) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting another admin
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin accounts.');
        }

        $user->delete();

        return back()->with('success', "User \"{$user->name}\" has been deleted.");
    }
}
