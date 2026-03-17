<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;

class InvoiceService
{
    public function generateReference(): string
    {
        $year  = now()->year;
        $last  = Invoice::whereYear('created_at', $year)->count();
        $num   = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
        return "FAC-{$year}-{$num}";
    }

    public function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();
        foreach ($items as $item) {
            if (!empty($item['description'])) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $item['quantity']   ?? 1,
                    'unit_price'  => $item['unit_price'] ?? 0,
                ]);
            }
        }
    }

    public function createFromQuote(Quote $quote): Invoice
    {
        $invoice = Invoice::create([
            'reference'  => $this->generateReference(),
            'title'      => $quote->title,
            'client_id'  => $quote->client_id,
            'quote_id'   => $quote->id,
            'project_id' => $quote->project_id ?? null,
            'created_by' => auth()->id(),
            'status'     => 'en_attente',
            'issue_date' => now(),
            'due_date'   => now()->addDays(30),
            'tax_rate'   => $quote->tax_rate ?? 20,
            'notes'      => $quote->notes ?? null,
        ]);

        foreach ($quote->items as $item) {
            $invoice->items()->create([
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
            ]);
        }

        return $invoice;
    }
}