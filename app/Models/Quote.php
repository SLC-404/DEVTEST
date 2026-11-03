<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = ['client_id', 'subtotal', 'taxes', 'total', 'exchange_rate', 'order_id'];

    protected function casts(): array
    {
        return [
            'subtotal'      => 'decimal:2',
            'taxes'         => 'decimal:2',
            'total'         => 'decimal:2',
            'exchange_rate' => 'decimal:4',
        ];
    }
    #[Scope]
    protected function open($query)
    {
        return $query->whereNull('order_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
