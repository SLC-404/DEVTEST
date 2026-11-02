<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'folio',
        'client_id',
        'email',
        'subtotal',
        'taxes',
        'total',
        'exchange_rate',
        'status',
        'payment_reference',
        'ordered_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'taxes' => 'decimal:2',
            'total' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'ordered_at' => 'datetime'
        ];
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
