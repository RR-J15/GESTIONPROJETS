<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .title { font-size: 22px; font-weight: bold; color: #4f46e5; }
        .reference { font-size: 14px; color: #6b7280; margin-top: 4px; }
        .section { margin-bottom: 20px; }
        .label { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .totals { width: 250px; margin-left: auto; margin-top: 20px; }
        .totals tr td { border: none; padding: 4px 8px; }
        .total-ttc { font-size: 14px; font-weight: bold; border-top: 2px solid #1f2937; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <div class="title">DEVIS</div>
            <div class="reference">{{ $quote->reference }}</div>
        </div>
        <div class="text-right">
            <div style="font-size:14px; font-weight:bold;">CameleonLab</div>
            <div class="label">Émis le {{ $quote->issue_date->format('d/m/Y') }}</div>
            <div class="label">Expire le {{ $quote->expiry_date->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Client --}}
    <div class="section">
        <div class="label">CLIENT</div>
        <div style="font-weight:bold;">{{ $quote->client->company_name }}</div>
        @if($quote->client->contact_name)
            <div>{{ $quote->client->contact_name }}</div>
        @endif
        @if($quote->client->email)
            <div>{{ $quote->client->email }}</div>
        @endif
        @if($quote->client->address)
            <div>{{ $quote->client->address }}</div>
        @endif
    </div>

    {{-- Titre --}}
    <div class="section">
        <div class="label">OBJET</div>
        <div>{{ $quote->title }}</div>
    </div>

    {{-- Lignes --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix HT</th>
                <th class="text-right">Remise</th>
                <th class="text-right">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                    <td class="text-right">{{ $item->discount }} %</td>
                    <td class="text-right">{{ number_format($item->total_ht, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totaux --}}
    <table class="totals">
        <tr>
            <td class="label">Sous-total HT</td>
            <td class="text-right">{{ number_format($quote->subtotal, 2, ',', ' ') }} €</td>
        </tr>
        @if($quote->discount_global > 0)
            <tr>
                <td class="label">Remise ({{ $quote->discount_global }}%)</td>
                <td class="text-right">- {{ number_format($quote->subtotal - $quote->total_ht, 2, ',', ' ') }} €</td>
            </tr>
        @endif
        <tr>
            <td class="label">Total HT</td>
            <td class="text-right">{{ number_format($quote->total_ht, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td class="label">TVA ({{ $quote->tax_rate }}%)</td>
            <td class="text-right">{{ number_format($quote->tva_amount, 2, ',', ' ') }} €</td>
        </tr>
        <tr class="total-ttc">
            <td>Total TTC</td>
            <td class="text-right">{{ number_format($quote->total_ttc, 2, ',', ' ') }} €</td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($quote->notes)
        <div class="section" style="margin-top:30px;">
            <div class="label">NOTES</div>
            <div>{{ $quote->notes }}</div>
        </div>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>