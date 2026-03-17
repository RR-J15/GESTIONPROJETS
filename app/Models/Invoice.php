<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'reference', 'title', 'client_id', 'quote_id', 'project_id',
        'created_by', 'status', 'issue_date', 'due_date', 'tax_rate', 'notes',
    ];

    protected $casts = [
        'status'     => InvoiceStatus::class,
        'issue_date' => 'date',
        'due_date'   => 'date',
        'tax_rate'   => 'float',
    ];

    public function client()   { return $this->belongsTo(Client::class); }
    public function quote()    { return $this->belongsTo(Quote::class); }
    public function project()  { return $this->belongsTo(Project::class); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function items()    { return $this->hasMany(InvoiceItem::class); }

    // Accesseurs calculés
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($i) => $i->quantity * $i->unit_price);
    }

    public function getTvaAmountAttribute(): float
    {
        return round($this->subtotal * ($this->tax_rate / 100), 2);
    }

    public function getTotalTtcAttribute(): float
    {
        return round($this->subtotal + $this->tva_amount, 2);
    }

    public function isEnRetard(): bool
    {
        return $this->status !== InvoiceStatus::Payee
            && $this->due_date
            && $this->due_date->isPast();
    }
}