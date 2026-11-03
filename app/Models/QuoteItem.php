<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = ['quote_id', 'product_id', 'quantity', 'cost_usd', 'price_usd', 'price_mxn'];
    protected function casts(): array
    {
        return [
            'quantity'  => 'integer',
            'cost_usd'  => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_mxn' => 'decimal:2',
        ];
    }
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
