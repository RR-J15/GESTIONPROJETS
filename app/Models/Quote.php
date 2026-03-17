<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'created_by',
        'reference',
        'title',
        'status',
        'issue_date',
        'expiry_date',
        'tax_rate',
        'discount_global',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'      => QuoteStatus::class,
            'issue_date'  => 'date',
            'expiry_date' => 'date',
        ];
    }

    // ===== Relations =====

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    // ===== Calculs =====

    // Sous-total HT avant remise globale
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) =>
            $item->quantity * $item->unit_price * (1 - $item->discount / 100)
        );
    }

    // Total HT après remise globale
    public function getTotalHtAttribute(): float
    {
        return $this->subtotal * (1 - $this->discount_global / 100);
    }

    // Montant TVA
    public function getTvaAmountAttribute(): float
    {
        return $this->total_ht * ($this->tax_rate / 100);
    }

    // Total TTC
    public function getTotalTtcAttribute(): float
    {
        return $this->total_ht + $this->tva_amount;
    }

    // ===== Helpers Statut =====

    public function isAccepte(): bool
    {
        return $this->status === QuoteStatus::Accepte;
    }

    public function isBrouillon(): bool
    {
        return $this->status === QuoteStatus::Brouillon;
    }
}