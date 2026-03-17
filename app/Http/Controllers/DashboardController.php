<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\QuoteStatus;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::Admin  => $this->adminDashboard(),
            UserRole::Membre => $this->membreDashboard($user),
        };
    }

    // ===================================================
    // ADMIN
    // ===================================================
    private function adminDashboard()
    {
        // KPIs Projets
        $totalProjects    = Project::count();
        $activeProjects   = Project::where('status', ProjectStatus::EnCours->value)->count();
        $lateProjects     = Project::where('status', '!=', ProjectStatus::Termine->value)
            ->where('end_date', '<', now())
            ->count();
        $finishedThisMonth = Project::where('status', ProjectStatus::Termine->value)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // KPIs Devis
        $totalQuotes     = Quote::count();
        $pendingQuotes   = Quote::where('status', QuoteStatus::Envoye->value)->count();
        $acceptedQuotes  = Quote::where('status', QuoteStatus::Accepte->value)->count();
        $conversionRate  = $totalQuotes > 0
            ? round(($acceptedQuotes / $totalQuotes) * 100, 1)
            : 0;

        // CA prévisionnel
        $revenue = Quote::where('status', QuoteStatus::Accepte->value)
            ->with('items')
            ->get()
            ->sum(fn($q) => $q->total_ttc);

        // Graphique CA mensuel — 12 derniers mois
        $caByMonth = collect(range(11, 0))->map(function ($monthsAgo) {
            $date     = now()->subMonths($monthsAgo);
            $accepted = Quote::where('status', QuoteStatus::Accepte->value)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->with('items')
                ->get()
                ->sum(fn($q) => $q->total_ttc);
            return [
                'month' => $date->translatedFormat('M Y'),
                'ca'    => round($accepted, 2),
            ];
        });

        // Derniers projets
        $recentProjects = Project::with('client', 'members')
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Derniers devis
        $recentQuotes = Quote::with('client')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Notifications
        $unreadNotifications = auth()->user()
            ->unreadNotifications()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalProjects',
            'activeProjects',
            'lateProjects',
            'finishedThisMonth',
            'totalQuotes',
            'pendingQuotes',
            'acceptedQuotes',
            'conversionRate',
            'revenue',
            'caByMonth',
            'recentProjects',
            'recentQuotes',
            'unreadNotifications',
        ));
    }

    // ===================================================
    // CHEF DE PROJET
    // ===================================================
    private function chefProjetDashboard($user)
    {
        // Projets dont il est membre
        $myProjects = $user->projects()
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeProjects = $myProjects->where('status.value', ProjectStatus::EnCours->value)->count();
        $lateProjects   = $myProjects->filter(
            fn($p) =>
            $p->status->value !== ProjectStatus::Termine->value
                && $p->end_date->isPast()
        )->count();

        // Ses tâches assignées
        $myTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', [
                TaskStatus::AFaire->value,
                TaskStatus::EnCours->value,
                TaskStatus::EnRevision->value,
            ])
            ->with('project')
            ->orderBy('due_date')
            ->take(6)
            ->get();

        $lateTasks = Task::where('assigned_to', $user->id)
            ->where('status', '!=', TaskStatus::Termine->value)
            ->where('due_date', '<', now())
            ->count();

        // Ses devis
        $myQuotes = Quote::where('created_by', $user->id)
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pendingQuotes = Quote::where('created_by', $user->id)
            ->where('status', QuoteStatus::Envoye->value)
            ->count();

        // Notifications
        $unreadNotifications = $user->unreadNotifications()->take(5)->get();

        return view('dashboard.chef_projet', compact(
            'myProjects',
            'activeProjects',
            'lateProjects',
            'myTasks',
            'lateTasks',
            'myQuotes',
            'pendingQuotes',
            'unreadNotifications',
        ));
    }

    // ===================================================
    // MEMBRE
    // ===================================================
    private function membreDashboard($user)
    {
        // Tâches assignées
        $myTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', [
                TaskStatus::AFaire->value,
                TaskStatus::EnCours->value,
                TaskStatus::EnRevision->value,
            ])
            ->with('project')
            ->orderBy('due_date')
            ->get();

        $totalTasks  = $myTasks->count();
        $lateTasks   = $myTasks->filter(fn($t) => $t->isEnRetard())->count();
        $urgentTasks = $myTasks->filter(fn($t) => $t->priority->value === 'critique')->count();

        // Projets dont il est membre
        $myProjects = $user->projects()
            ->withCount('tasks')
            ->with('members')
            ->orderBy('created_at', 'desc')
            ->get();

        // Notifications
        $unreadNotifications = $user->unreadNotifications()->take(5)->get();

        return view('dashboard.membre', compact(
            'myTasks',
            'totalTasks',
            'lateTasks',
            'urgentTasks',
            'myProjects',
            'unreadNotifications',
        ));
    }
}
