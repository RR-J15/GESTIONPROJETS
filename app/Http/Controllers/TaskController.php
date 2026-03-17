<?php

namespace App\Http\Controllers;

use App\Notifications\TaskAssignedNotification;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Liste les tâches d'un projet
    public function index(Project $project)
    {
        $tasks = $project->tasks()
            ->with('assignedTo')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tasks.index', compact('project', 'tasks'));
    }

    // Formulaire création
    public function create(Project $project)
    {
        $members = $project->members()->orderBy('name')->get();

        return view('tasks.create', compact('project', 'members'));
    }

    // Enregistre une nouvelle tâche
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create([
            'assigned_to' => $request->assigned_to,
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority,
            'due_date'    => $request->due_date,
        ]);

        // Notifier le membre assigné
        if ($request->assigned_to) {
            $assignedUser = User::find($request->assigned_to);
            if ($assignedUser && $assignedUser->id !== auth()->id()) {
                $assignedUser->notify(new TaskAssignedNotification($task));
            }
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche créée avec succès.');
    }

    // Détail d'une tâche
    public function show(Task $task)
    {
        $task->load([
            'project',
            'assignedTo',
            'createdBy',
            'comments' => fn($q) => $q->with('user')->orderBy('created_at', 'asc'),
            'attachments' => fn($q) => $q->with('user')->orderBy('created_at', 'desc'),
        ]);

        $members = $task->project->members()->orderBy('name')->get();

        return view('tasks.show', compact('task', 'members'));
    }

    // Formulaire édition
    public function edit(Task $task)
    {
        $members = $task->project->members()->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'members'));
    }

    // Met à jour une tâche
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update([
            'assigned_to' => $request->assigned_to,
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority,
            'due_date'    => $request->due_date,
        ]);

        // Notifier si l'assignation change
        if ($request->assigned_to && $request->assigned_to != $task->getOriginal('assigned_to')) {
            $assignedUser = User::find($request->assigned_to);
            if ($assignedUser && $assignedUser->id !== auth()->id()) {
                $assignedUser->notify(new TaskAssignedNotification($task));
            }
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Tâche mise à jour.');
    }

    // Supprime une tâche
    public function destroy(Task $task)
    {
        $project = $task->project;

        // Supprimer les fichiers attachés
        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
        }

        $task->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche supprimée.');
    }

    // Ajoute un commentaire
    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Commentaire ajouté.');
    }

    // Ajoute une pièce jointe
    public function addAttachment(Request $request, Task $task)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments/tasks/' . $task->id, 'public');

        $task->attachments()->create([
            'user_id'   => auth()->id(),
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Fichier ajouté.');
    }
}
