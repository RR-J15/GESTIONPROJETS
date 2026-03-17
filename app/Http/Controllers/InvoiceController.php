<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $service) {}

    public function index()
    {
        $invoices = Invoice::with('client', 'quote')
                           ->withSum('items', 'unit_price')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        // Auto-passer en retard si dépassée
        Invoice::where('status', InvoiceStatus::EnAttente->value)
               ->where('due_date', '<', now())
               ->update(['status' => InvoiceStatus::EnRetard->value]);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::orderBy('company_name')->get();
        $quotes  = Quote::where('status', 'accepte')->with('client')->get();
        return view('invoices.create', compact('clients', 'quotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'client_id'  => 'required|exists:clients,id',
            'quote_id'   => 'nullable|exists:quotes,id',
            'issue_date' => 'required|date',
            'due_date'   => 'nullable|date|after_or_equal:issue_date',
            'tax_rate'   => 'required|numeric|min:0|max:100',
            'notes'      => 'nullable|string',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            ...$data,
            'reference'  => $this->service->generateReference(),
            'created_by' => auth()->id(),
            'status'     => 'en_attente',
        ]);

        $this->service->syncItems($invoice, $request->items);

        return redirect()->route('invoices.show', $invoice)
                         ->with('success', 'Facture créée avec succès.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client', 'items', 'quote', 'creator');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::Payee) {
            return back()->with('error', 'Une facture payée ne peut pas être modifiée.');
        }
        $clients = Client::orderBy('company_name')->get();
        $quotes  = Quote::where('status', 'accepte')->with('client')->get();
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'clients', 'quotes'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::Payee) {
            return back()->with('error', 'Une facture payée ne peut pas être modifiée.');
        }

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'client_id'  => 'required|exists:clients,id',
            'quote_id'   => 'nullable|exists:quotes,id',
            'issue_date' => 'required|date',
            'due_date'   => 'nullable|date|after_or_equal:issue_date',
            'tax_rate'   => 'required|numeric|min:0|max:100',
            'notes'      => 'nullable|string',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $invoice->update($data);
        $this->service->syncItems($invoice, $request->items);

        return redirect()->route('invoices.show', $invoice)
                         ->with('success', 'Facture mise à jour.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::Payee) {
            return back()->with('error', 'Une facture payée ne peut pas être supprimée.');
        }
        $invoice->delete();
        return redirect()->route('invoices.index')
                         ->with('success', 'Facture supprimée.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:en_attente,payee,en_retard',
        ]);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Statut mis à jour.');
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load('client', 'items', 'quote', 'creator');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download("facture-{$invoice->reference}.pdf");
    }
}