<?php

namespace App\Http\Controllers;


use App\Notifications\QuoteStatusChangedNotification;
use App\Enums\QuoteStatus;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Client;
use App\Models\Quote;
use App\Services\QuoteService;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function __construct(protected QuoteService $quoteService) {}

    // Liste tous les devis
    public function index()
    {
        $quotes = Quote::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('quotes.index', compact('quotes'));
    }

    // Formulaire création
    public function create()
    {
        $clients     = Client::orderBy('company_name')->get();
        $client_id   = request('client_id');

        return view('quotes.create', compact('clients', 'client_id'));
    }

    // Enregistre un nouveau devis
    public function store(StoreQuoteRequest $request)
    {
        $quote = Quote::create([
            'client_id'       => $request->client_id,
            'created_by'      => auth()->id(),
            'reference'       => $this->quoteService->generateReference(),
            'title'           => $request->title,
            'status'          => QuoteStatus::Brouillon->value,
            'issue_date'      => $request->issue_date,
            'expiry_date'     => $request->expiry_date,
            'tax_rate'        => $request->tax_rate,
            'discount_global' => $request->discount_global ?? 0,
            'notes'           => $request->notes,
        ]);

        $this->quoteService->syncItems($quote, $request->items);

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Devis créé avec succès.');
    }

    // Détail d'un devis
    public function show(Quote $quote)
    {
        $quote->load('client', 'items', 'createdBy', 'project');

        return view('quotes.show', compact('quote'));
    }

    // Formulaire édition
    public function edit(Quote $quote)
    {
        // Un devis accepté ou refusé ne peut plus être modifié
        if (in_array($quote->status, [QuoteStatus::Accepte, QuoteStatus::Refuse])) {
            return redirect()
                ->route('quotes.show', $quote)
                ->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $clients = Client::orderBy('company_name')->get();
        $quote->load('items');

        return view('quotes.edit', compact('quote', 'clients'));
    }

    // Met à jour un devis
    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        $quote->update([
            'client_id'       => $request->client_id,
            'title'           => $request->title,
            'issue_date'      => $request->issue_date,
            'expiry_date'     => $request->expiry_date,
            'tax_rate'        => $request->tax_rate,
            'discount_global' => $request->discount_global ?? 0,
            'notes'           => $request->notes,
        ]);

        $this->quoteService->syncItems($quote, $request->items);

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Devis mis à jour avec succès.');
    }

    // Supprime un devis
    public function destroy(Quote $quote)
    {
        if ($quote->status !== QuoteStatus::Brouillon) {
            return redirect()
                ->route('quotes.index')
                ->with('error', 'Seul un devis en brouillon peut être supprimé.');
        }

        $quote->delete();

        return redirect()
            ->route('quotes.index')
            ->with('success', 'Devis supprimé.');
    }

    // Met à jour le statut
    public function updateStatus(Quote $quote)
    {
        $status = request()->validate([
            'status' => ['required', 'in:brouillon,envoye,accepte,refuse,expire'],
        ])['status'];

        $quote->update(['status' => $status]);

        // Notifier le créateur du devis
        if ($quote->createdBy->id !== auth()->id()) {
            $quote->createdBy->notify(new QuoteStatusChangedNotification($quote));
        }

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Statut mis à jour.');
    }

    // Convertit en projet
    public function convertToProject(Quote $quote)
    {
        if ($quote->status !== QuoteStatus::Accepte) {
            return redirect()
                ->route('quotes.show', $quote)
                ->with('error', 'Seul un devis accepté peut être converti en projet.');
        }

        if ($quote->project) {
            return redirect()
                ->route('projects.show', $quote->project)
                ->with('error', 'Ce devis a déjà été converti en projet.');
        }

        $project = $this->quoteService->convertToProject($quote);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet créé depuis le devis ' . $quote->reference);
    }

    // Export PDF
    public function exportPdf(Quote $quote)
    {
        $quote->load('client', 'items', 'createdBy');

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('devis-' . $quote->reference . '.pdf');
    }
}
