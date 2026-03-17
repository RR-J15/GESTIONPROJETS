<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; background: #fff; }
        .container { padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .company-name { font-size: 22px; font-weight: 700; color: #4f46e5; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px; }
        .invoice-title .ref { font-size: 13px; color: #64748b; margin-top: 4px; }

        /* Badge statut */
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 8px; }
        .status-en_attente { background: #fef3c7; color: #92400e; }
        .status-payee      { background: #d1fae5; color: #065f46; }
        .status-en_retard  { background: #fee2e2; color: #991b1b; }

        /* Divider */
        .divider { border: none; border-top: 2px solid #4f46e5; margin: 24px 0; }

        /* Parties */
        .parties { display: flex; justify-content: space-between; margin-bottom: 32px; }
        .party { width: 48%; }
        .party-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .party-name  { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .party-info  { color: #64748b; line-height: 1.6; }

        /* Dates */
        .dates { display: flex; gap: 24px; margin-bottom: 32px; }
        .date-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 20px; }
        .date-label { font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; }
        .date-value { font-size: 13px; font-weight: 600; color: #1e293b; margin-top: 2px; }

        /* Devis lié */
        .quote-ref { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px;
                     padding: 10px 16px; margin-bottom: 24px; color: #4338ca; font-size: 12px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1e293b; color: white; }
        thead th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:last-child { text-align: right; }
        thead th:nth-child(2), thead th:nth-child(3) { text-align: center; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 11px 16px; border-bottom: 1px solid #f1f5f9; }
        tbody td:nth-child(2) { text-align: center; color: #64748b; }
        tbody td:nth-child(3) { text-align: right; color: #64748b; }
        tbody td:last-child { text-align: right; font-weight: 600; }

        /* Totaux */
        .totals { display: flex; justify-content: flex-end; margin-bottom: 32px; }
        .totals-box { width: 260px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; }
        .total-row.subtotal { color: #64748b; }
        .total-row.tva      { color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 4px; }
        .total-row.ttc      { font-size: 15px; font-weight: 700; color: #4f46e5; padding-top: 8px; }

        /* Notes */
        .notes { background: #f8fafc; border-left: 3px solid #4f46e5; padding: 12px 16px;
                 border-radius: 0 8px 8px 0; margin-bottom: 32px; }
        .notes-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; }
        .notes-text  { color: #475569; line-height: 1.6; }

        /* Footer */
        .footer { border-top: 1px solid #e2e8f0; padding-top: 20px; text-align: center; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="company-name">{{ config('app.name') }}</div>
        </div>
        <div class="invoice-title">
            <h1>FACTURE</h1>
            <div class="ref">{{ $invoice->reference }}</div>
            @php
                $statusLabels = ['en_attente' => 'En attente', 'payee' => 'Payée', 'en_retard' => 'En retard'];
            @endphp
            <div class="status-badge status-{{ $invoice->status->value }}">
                {{ $statusLabels[$invoice->status->value] }}
            </div>
        </div>
    </div>

    <hr class="divider">

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Émetteur</div>
            <div class="party-name">{{ config('app.name') }}</div>
            <div class="party-info">Créée par {{ $invoice->creator->name }}</div>
        </div>
        <div class="party">
            <div class="party-label">Facturer à</div>
            <div class="party-name">{{ $invoice->client->company_name }}</div>
            <div class="party-info">
                @if($invoice->client->contact_name){{ $invoice->client->contact_name }}<br>@endif
                @if($invoice->client->email){{ $invoice->client->email }}<br>@endif
                @if($invoice->client->phone){{ $invoice->client->phone }}<br>@endif
                @if($invoice->client->address){{ $invoice->client->address }}@endif
            </div>
        </div>
    </div>

    {{-- Dates --}}
    <div class="dates">
        <div class="date-box">
            <div class="date-label">Date d'émission</div>
            <div class="date-value">{{ $invoice->issue_date->format('d/m/Y') }}</div>
        </div>
        @if($invoice->due_date)
            <div class="date-box">
                <div class="date-label">Date d'échéance</div>
                <div class="date-value">{{ $invoice->due_date->format('d/m/Y') }}</div>
            </div>
        @endif
    </div>

    {{-- Devis lié --}}
    @if($invoice->quote)
        <div class="quote-ref">
            📄 Facture liée au devis <strong>{{ $invoice->quote->reference }}</strong>
            — {{ $invoice->quote->title }}
        </div>
    @endif

    {{-- Lignes --}}
    <table>
        <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th style="width:10%">Qté</th>
                <th style="width:20%">Prix unitaire HT</th>
                <th style="width:20%">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                    <td>{{ number_format($item->total, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totals">
        <div class="totals-box">
            <div class="total-row subtotal">
                <span>Sous-total HT</span>
                <span>{{ number_format($invoice->subtotal, 2, ',', ' ') }} €</span>
            </div>
            <div class="total-row tva">
                <span>TVA ({{ $invoice->tax_rate }}%)</span>
                <span>{{ number_format($invoice->tva_amount, 2, ',', ' ') }} €</span>
            </div>
            <div class="total-row ttc">
                <span>Total TTC</span>
                <span>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} €</span>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($invoice->notes)
        <div class="notes">
            <div class="notes-label">Notes & conditions</div>
            <div class="notes-text">{{ $invoice->notes }}</div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>{{ config('app.name') }} — Facture {{ $invoice->reference }} — Générée le {{ now()->format('d/m/Y') }}</p>
    </div>

</div>
</body>
</html>