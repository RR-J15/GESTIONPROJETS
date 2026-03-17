<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:admin,chef_projet,membre'],
        ]);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $user->update(['role' => $request->role]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Rôle mis à jour pour ' . $user->name);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}