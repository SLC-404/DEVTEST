<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory,SoftDeletes;

    protected $fillable = [
            'category_id',
            'sku',
            'name',
            'short_description',
            'long_description',
            'cost_usd',
            'price_usd',
            'price_mxm',
            'image',
            'stock',
            'valid_until',
            'is_active'
    ];

    protected function casts():array{
        return[
            'cost_usd'=>'decimal:2',
            'price_usd'=>'decimal:2',
            'price_mxm'=>'decimal:2',
            'stock'=>'integer',
            'valid_until'=>'date',
            'is_active'=>'boolean'
        ];
    }
    public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }
    public function orderDetails(): HasMany{
        return $this->hasMany(OrderDetail::class);
    }
    public function kardexPrices(): HasMany{
        return $this->hasMany(KardexPrice::class);
    }
    public function kardexInventory(): HasMany{
        return $this->hasMany(KardexInventory::class);
    }
}
