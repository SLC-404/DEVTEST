<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['date', 'base_currency', 'quote_currency', 'rate', 'source'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'rate' => 'decimal:4',
        ];
    }
}
