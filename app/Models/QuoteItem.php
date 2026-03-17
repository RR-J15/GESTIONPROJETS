<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'float',
            'unit_price' => 'float',
            'discount'   => 'float',
        ];
    }

    // ===== Relations =====

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    // ===== Calculs =====

    // Total HT de la ligne
    public function getTotalHtAttribute(): float
    {
        return $this->quantity * $this->unit_price * (1 - $this->discount / 100);
    }
}