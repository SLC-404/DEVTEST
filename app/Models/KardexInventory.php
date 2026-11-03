<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KardexInventory extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'order_id', 'type',
        'quantity', 'stock_before', 'stock_after', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'stock_before' => 'integer',
            'stock_after'  => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
