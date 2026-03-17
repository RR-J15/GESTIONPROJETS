<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\ProjectStatus;
use App\Enums\QuoteStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== UTILISATEURS =====
        $admin = User::create([
            'name'     => 'Admin Principal',
            'email'    => 'admin@cameleon.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::Admin,
            'position' => 'Directeur',
        ]);

        $membre1 = User::create([
            'name'     => 'Jean Dupont',
            'email'    => 'jean@cameleon.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::Membre,
            'position' => 'Développeur',
        ]);

        $membre2 = User::create([
            'name'     => 'Marie Martin',
            'email'    => 'marie@cameleon.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::Membre,
            'position' => 'Designer',
        ]);

        $membre3 = User::create([
            'name'     => 'Pierre Durand',
            'email'    => 'pierre@cameleon.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::Membre,
            'position' => 'Chef de projet',
        ]);

        // ===== CLIENTS =====
        $client1 = Client::create([
            'company_name' => 'TechCorp Solutions',
            'contact_name' => 'Alice Bernard',
            'email'        => 'alice@techcorp.com',
            'phone'        => '+33 1 23 45 67 89',
            'address'      => '12 rue de la Paix, 75001 Paris',
        ]);

        $client2 = Client::create([
            'company_name' => 'StartUp Innov',
            'contact_name' => 'Bob Leclerc',
            'email'        => 'bob@startup-innov.fr',
            'phone'        => '+33 6 12 34 56 78',
            'address'      => '45 avenue des Champs, 69001 Lyon',
        ]);

        $client3 = Client::create([
            'company_name' => 'Agence Créative',
            'contact_name' => 'Clara Petit',
            'email'        => 'clara@agence-creative.fr',
            'phone'        => '+33 5 98 76 54 32',
            'address'      => '8 place du Capitole, 31000 Toulouse',
        ]);

        // ===== DEVIS =====
        $quote1 = Quote::create([
            'reference'   => 'DEV-2026-001',
            'title'       => 'Développement site e-commerce',
            'client_id'   => $client1->id,
            'created_by'  => $admin->id,
            'status'      => QuoteStatus::Accepte,
            'tax_rate'    => 20,
            'issue_date'  => now()->subDays(30),
            'expiry_date' => now()->addDays(30),
            'notes'       => 'Projet urgent — livraison sous 3 mois.',
        ]);
        QuoteItem::create(['quote_id' => $quote1->id, 'description' => 'Développement frontend', 'quantity' => 1,  'unit_price' => 4500]);
        QuoteItem::create(['quote_id' => $quote1->id, 'description' => 'Développement backend', 'quantity' => 1,  'unit_price' => 5500]);
        QuoteItem::create(['quote_id' => $quote1->id, 'description' => 'Intégration paiement',  'quantity' => 1,  'unit_price' => 1200]);

        $quote2 = Quote::create([
            'reference'   => 'DEV-2026-002',
            'title'       => 'Refonte identité visuelle',
            'client_id'   => $client2->id,
            'created_by'  => $admin->id,
            'status'      => QuoteStatus::Envoye,
            'tax_rate'    => 20,
            'issue_date'  => now()->subDays(10),
            'expiry_date' => now()->addDays(15),
            'notes'       => 'Inclut logo, charte graphique et supports.',
        ]);
        QuoteItem::create(['quote_id' => $quote2->id, 'description' => 'Création logo',       'quantity' => 1,  'unit_price' => 800]);
        QuoteItem::create(['quote_id' => $quote2->id, 'description' => 'Charte graphique',    'quantity' => 1,  'unit_price' => 1200]);
        QuoteItem::create(['quote_id' => $quote2->id, 'description' => 'Supports print x10', 'quantity' => 10, 'unit_price' => 150]);

        $quote3 = Quote::create([
            'reference'   => 'DEV-2026-003',
            'title'       => 'Application mobile iOS/Android',
            'client_id'   => $client3->id,
            'created_by'  => $admin->id,
            'status'      => QuoteStatus::Brouillon,
            'tax_rate'    => 20,
            'issue_date'  => now()->subDays(2),
            'expiry_date' => now()->addDays(60),
        ]);
        QuoteItem::create(['quote_id' => $quote3->id, 'description' => 'Design UX/UI',      'quantity' => 1, 'unit_price' => 3000]);
        QuoteItem::create(['quote_id' => $quote3->id, 'description' => 'Développement app', 'quantity' => 1, 'unit_price' => 8000]);

        $quote4 = Quote::create([
            'reference'   => 'DEV-2026-004',
            'title'       => 'Maintenance annuelle',
            'client_id'   => $client1->id,
            'created_by'  => $admin->id,
            'status'      => QuoteStatus::Refuse,
            'tax_rate'    => 20,
            'issue_date'  => now()->subDays(40),
            'expiry_date' => now()->subDays(10),
        ]);
        QuoteItem::create(['quote_id' => $quote4->id, 'description' => 'Maintenance mensuelle', 'quantity' => 12, 'unit_price' => 300]);

        // ===== FACTURES =====
        $invoice1 = Invoice::create([
            'reference'  => 'FAC-2026-001',
            'title'      => 'Développement site e-commerce',
            'client_id'  => $client1->id,
            'quote_id'   => $quote1->id,
            'created_by' => $admin->id,
            'status'     => InvoiceStatus::Payee,
            'issue_date' => now()->subDays(30),
            'due_date'   => now()->subDays(5),
            'tax_rate'   => 20,
            'notes'      => 'Paiement reçu par virement bancaire.',
        ]);
        InvoiceItem::create(['invoice_id' => $invoice1->id, 'description' => 'Développement frontend', 'quantity' => 1, 'unit_price' => 4500]);
        InvoiceItem::create(['invoice_id' => $invoice1->id, 'description' => 'Développement backend', 'quantity' => 1, 'unit_price' => 5500]);
        InvoiceItem::create(['invoice_id' => $invoice1->id, 'description' => 'Intégration paiement',  'quantity' => 1, 'unit_price' => 1200]);

        $invoice2 = Invoice::create([
            'reference'  => 'FAC-2026-002',
            'title'      => 'Refonte identité visuelle — Acompte',
            'client_id'  => $client2->id,
            'created_by' => $admin->id,
            'status'     => InvoiceStatus::EnAttente,
            'issue_date' => now()->subDays(5),
            'due_date'   => now()->addDays(25),
            'tax_rate'   => 20,
        ]);
        InvoiceItem::create(['invoice_id' => $invoice2->id, 'description' => 'Acompte 50% — Identité visuelle', 'quantity' => 1, 'unit_price' => 1100]);

        $invoice3 = Invoice::create([
            'reference'  => 'FAC-2026-003',
            'title'      => 'Prestation conseil',
            'client_id'  => $client3->id,
            'created_by' => $admin->id,
            'status'     => InvoiceStatus::EnRetard,
            'issue_date' => now()->subDays(45),
            'due_date'   => now()->subDays(15),
            'tax_rate'   => 20,
            'notes'      => 'Relance envoyée le ' . now()->subDays(5)->format('d/m/Y'),
        ]);
        InvoiceItem::create(['invoice_id' => $invoice3->id, 'description' => 'Conseil stratégique', 'quantity' => 3, 'unit_price' => 500]);
        InvoiceItem::create(['invoice_id' => $invoice3->id, 'description' => 'Rapport d\'analyse',  'quantity' => 1, 'unit_price' => 800]);

        // ===== PROJETS =====
        $project1 = Project::create([
            'name'        => 'Site E-Commerce TechCorp',
            'description' => 'Développement complet de la boutique en ligne.',
            'client_id'   => $client1->id,
            'created_by'  => $admin->id,
            'status'      => ProjectStatus::EnCours,
            'start_date'  => now()->subDays(20),
            'end_date'    => now()->addDays(70),
            'budget'      => 11200,
        ]);
        $project1->members()->attach([
            $admin->id   => ['role' => 'chef'],
            $membre1->id => ['role' => 'membre'],
            $membre2->id => ['role' => 'membre'],
        ]);

        $project2 = Project::create([
            'name'        => 'Refonte Identité StartUp Innov',
            'description' => 'Nouvelle charte graphique et supports de communication.',
            'client_id'   => $client2->id,
            'created_by'  => $admin->id,
            'status'      => ProjectStatus::EnCours,
            'start_date'  => now()->subDays(10),
            'end_date'    => now()->addDays(30),
            'budget'      => 3500,
        ]);
        $project2->members()->attach([
            $admin->id   => ['role' => 'chef'],
            $membre2->id => ['role' => 'membre'],
            $membre3->id => ['role' => 'membre'],
        ]);

        $project3 = Project::create([
            'name'        => 'Application Mobile Agence Créative',
            'description' => 'App iOS et Android pour la gestion de contenu.',
            'client_id'   => $client3->id,
            'created_by'  => $admin->id,
            'status'      => ProjectStatus::Brouillon,
            'start_date'  => now()->addDays(15),
            'end_date'    => now()->addDays(120),
            'budget'      => 11000,
        ]);
        $project3->members()->attach([
            $admin->id   => ['role' => 'chef'],
            $membre1->id => ['role' => 'membre'],
        ]);

        $project4 = Project::create([
            'name'        => 'Audit Sécurité Annuel',
            'description' => 'Audit complet de l\'infrastructure.',
            'client_id'   => $client1->id,
            'created_by'  => $admin->id,
            'status'      => ProjectStatus::Termine,
            'start_date'  => now()->subDays(60),
            'end_date'    => now()->subDays(5),
            'budget'      => 2500,
        ]);
        $project4->members()->attach([
            $admin->id   => ['role' => 'chef'],
            $membre3->id => ['role' => 'membre'],
        ]);

        // ===== TÂCHES =====
        $task1 = Task::create([
            'title'       => 'Maquettes UI homepage',
            'description' => 'Créer les maquettes Figma de la page d\'accueil.',
            'project_id'  => $project1->id,
            'assigned_to' => $membre2->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::Termine,
            'priority'    => TaskPriority::Haute,
            'due_date'    => now()->subDays(5),
        ]);

        $task2 = Task::create([
            'title'       => 'Développement API produits',
            'description' => 'Créer les endpoints REST pour la gestion des produits.',
            'project_id'  => $project1->id,
            'assigned_to' => $membre1->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::EnCours,
            'priority'    => TaskPriority::Critique,
            'due_date'    => now()->addDays(7),
        ]);

        $task3 = Task::create([
            'title'       => 'Intégration paiement Stripe',
            'description' => 'Implémenter le module de paiement Stripe.',
            'project_id'  => $project1->id,
            'assigned_to' => $membre1->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::AFaire,
            'priority'    => TaskPriority::Haute,
            'due_date'    => now()->addDays(20),
        ]);

        Task::create([
            'title'       => 'Tests et recette',
            'description' => 'Tests fonctionnels complets avant livraison.',
            'project_id'  => $project1->id,
            'assigned_to' => $membre3->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::AFaire,
            'priority'    => TaskPriority::Normale,
            'due_date'    => now()->addDays(60),
        ]);

        Task::create([
            'title'       => 'Création logo principal',
            'description' => 'Design du nouveau logo en plusieurs variantes.',
            'project_id'  => $project2->id,
            'assigned_to' => $membre2->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::EnRevision,
            'priority'    => TaskPriority::Haute,
            'due_date'    => now()->addDays(3),
        ]);

        Task::create([
            'title'       => 'Charte typographique',
            'description' => 'Définir les polices et styles typographiques.',
            'project_id'  => $project2->id,
            'assigned_to' => $membre2->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::AFaire,
            'priority'    => TaskPriority::Normale,
            'due_date'    => now()->addDays(10),
        ]);

        Task::create([
            'title'       => 'Wireframes application',
            'description' => 'Créer les wireframes de toutes les écrans.',
            'project_id'  => $project3->id,
            'assigned_to' => $membre1->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::AFaire,
            'priority'    => TaskPriority::Normale,
            'due_date'    => now()->addDays(25),
        ]);

        Task::create([
            'title'       => 'Rapport audit sécurité',
            'description' => 'Rédiger le rapport final de l\'audit.',
            'project_id'  => $project1->id,
            'assigned_to' => $membre1->id,
            'created_by'  => $admin->id,
            'status'      => TaskStatus::EnCours,
            'priority'    => TaskPriority::Critique,
            'due_date'    => now()->subDays(3),
        ]);

        // ===== NOTIFICATIONS =====
        $admin->notify(new \App\Notifications\TaskAssignedNotification($task1));
        $membre1->notify(new \App\Notifications\TaskAssignedNotification($task2));
        $membre1->notify(new \App\Notifications\TaskAssignedNotification($task3));
        $membre2->notify(new \App\Notifications\TaskAssignedNotification($task1));
        $admin->notify(new \App\Notifications\QuoteStatusChangedNotification($quote1));

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('');
        $this->command->info('Comptes disponibles :');
        $this->command->info('  admin@cameleon.com  / password  (Admin)');
        $this->command->info('  jean@cameleon.com   / password  (Membre)');
        $this->command->info('  marie@cameleon.com  / password  (Membre)');
        $this->command->info('  pierre@cameleon.com / password  (Membre)');
    }
}