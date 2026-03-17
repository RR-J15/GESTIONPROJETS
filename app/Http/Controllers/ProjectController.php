<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    // Liste tous les projets
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $projects = Project::with('client', 'members')
                ->withCount('tasks')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $projects = $user->projects()
                ->with('client', 'members')
                ->withCount('tasks')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('projects.index', compact('projects'));
    }

    // Formulaire création
    public function create()
    {
        $clients   = Client::orderBy('company_name')->get();
        $client_id = request('client_id');

        return view('projects.create', compact('clients', 'client_id'));
    }

    // Enregistre un nouveau projet
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'client_id'   => $request->client_id,
            'quote_id'    => $request->quote_id,
            'created_by'  => auth()->id(),
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'budget'      => $request->budget,
        ]);

        // Ajouter le créateur comme chef du projet automatiquement
        $project->members()->attach(auth()->id(), ['role' => 'chef']);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    // Détail d'un projet
    public function show(Project $project)
    {
        $project->load([
            'client',
            'quote',
            'members',
            'tasks' => fn($q) => $q->with('assignedTo')->orderBy('created_at', 'desc'),
        ]);

        $availableMembers = User::whereNotIn('id', $project->members->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('projects.show', compact('project', 'availableMembers'));
    }

    // Formulaire édition
    public function edit(Project $project)
    {
        $clients = Client::orderBy('company_name')->get();

        return view('projects.edit', compact('project', 'clients'));
    }

    // Met à jour un projet
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update([
            'client_id'   => $request->client_id,
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'budget'      => $request->budget,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    // Supprime un projet
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet supprimé.');
    }

    // Ajoute un membre au projet
    public function addMember(Project $project)
    {
        $data = request()->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role'    => ['required', 'in:chef,membre'],
        ]);

        // Vérifier qu'il n'est pas déjà membre
        if ($project->members()->where('user_id', $data['user_id'])->exists()) {
            return redirect()
                ->route('projects.show', $project)
                ->with('error', 'Cet utilisateur est déjà membre du projet.');
        }

        $project->members()->attach($data['user_id'], ['role' => $data['role']]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Membre ajouté avec succès.');
    }

    // Retire un membre du projet
    public function removeMember(Project $project, User $user)
    {
        // On ne peut pas retirer le créateur du projet
        if ($project->created_by === $user->id) {
            return redirect()
                ->route('projects.show', $project)
                ->with('error', 'Le créateur du projet ne peut pas être retiré.');
        }

        $project->members()->detach($user->id);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Membre retiré du projet.');
    }
}
