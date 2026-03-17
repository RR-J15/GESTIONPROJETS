<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    // Liste des invitations — admin seulement
    public function index()
    {
        $invitations = Invitation::with('createdBy')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20);

        return view('admin.invitations.index', compact('invitations'));
    }

    // Créer une invitation
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:invitations,email', 'unique:users,email'],
            'role' => ['required', 'in:admin,membre'],
        ], [
            'email.unique' => 'Une invitation a déjà été envoyée à cet email ou cet utilisateur existe déjà.',
        ]);

        $invitation = Invitation::create([
            'created_by' => auth()->id(),
            'email'      => $request->email,
            'role'       => $request->role,
            'token'      => Str::random(64),
            'expires_at' => now()->addHours(48),
        ]);

        return redirect()
            ->route('admin.invitations.index')
            ->with('success', 'Invitation créée pour ' . $invitation->email);
    }

    // Supprime une invitation
    public function destroy(Invitation $invitation)
    {
        $invitation->delete();

        return redirect()
            ->route('admin.invitations.index')
            ->with('success', 'Invitation supprimée.');
    }

    // Page d'inscription via lien d'invitation
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return view('auth.invite-invalid');
        }

        return view('auth.invite', compact('invitation'));
    }

    // Créer le compte depuis l'invitation
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')
                             ->with('error', 'Ce lien d\'invitation est invalide ou expiré.');
        }

        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'password'              => ['required', 'confirmed', 'min:8'],
        ], [
            'name.required'         => 'Le nom est obligatoire.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.confirmed'    => 'Les mots de passe ne correspondent pas.',
            'password.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // Créer l'utilisateur avec le rôle défini dans l'invitation
        User::create([
            'name'     => $request->name,
            'email'    => $invitation->email,
            'password' => Hash::make($request->password),
            'role'     => $invitation->role,
        ]);

        // Invalider le token
        $invitation->update(['used_at' => now()]);

        return redirect()
            ->route('login')
            ->with('success', 'Compte créé avec succès. Connectez-vous !');
    }
}