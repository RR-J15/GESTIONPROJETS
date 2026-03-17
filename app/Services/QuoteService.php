<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\QuoteStatus;
use App\Models\Project;
use App\Models\Quote;

class QuoteService
{
    // Génère une référence unique ex: DEV-2026-001
    public function generateReference(): string
    {
        $year  = date('Y');
        $last  = Quote::whereYear('created_at', $year)->lockForUpdate()->count();
        $next  = str_pad($last + 1, 3, '0', STR_PAD_LEFT);

        return "DEV-{$year}-{$next}";
    }

    // Crée les lignes du devis
    public function syncItems(Quote $quote, array $items): void
    {
        $quote->items()->delete();

        foreach ($items as $item) {
            $quote->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'discount'    => $item['discount'] ?? 0,
            ]);
        }
    }

    // Convertit un devis accepté en projet
    public function convertToProject(Quote $quote): Project
    {
        $project = Project::create([
            'client_id'   => $quote->client_id,
            'quote_id'    => $quote->id,
            'created_by'  => auth()->id(),
            'name'        => $quote->title,
            'description' => 'Projet généré depuis le devis ' . $quote->reference,
            'status'      => ProjectStatus::Brouillon->value,
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addMonths(1)->toDateString(),
            'budget'      => $quote->total_ttc,
        ]);

        $quote->update(['status' => QuoteStatus::Accepte->value]);

        return $project;
    }
}