<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'short_description',
        'long_description',
        'cost_usd',
        'price_usd',
        'price_mxn',
        'image',
        'stock',
        'valid_until',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'cost_usd' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_mxn' => 'decimal:2',
            'stock' => 'integer',
            'valid_until' => 'date',
            'is_active' => 'boolean'
        ];
    }

    protected static function booted(): void
    {
        // Inventario inicial al crear el producto
        static::created(function (Product $product) {
            if ($product->stock > 0) {
                $product->kardexInventory()->create([
                    'user_id'      => Auth::id(),
                    'type'         => 'in',
                    'quantity'     => $product->stock,
                    'stock_before' => 0,
                    'stock_after'  => $product->stock,
                    'notes'        => 'Inventario inicial',
                ]);
            }
        });

        static::updated(function (Product $product) {
            // Kardex de precios
            $fields = [
                'sale_price'    => 'price_usd',
                'purchase_cost' => 'cost_usd',
            ];

            foreach ($fields as $type => $field) {
                if ($product->wasChanged($field)) {
                    $product->kardexPrices()->create([
                        'user_id'   => Auth::id(),
                        'type'      => $type,
                        'old_value' => $product->getOriginal($field),
                        'new_value' => $product->{$field},
                    ]);
                }
            }

            // Kardex de inventario (ajustes manuales)
            if ($product->wasChanged('stock')) {
                $before = (int) $product->getOriginal('stock');
                $after  = (int) $product->stock;

                $product->kardexInventory()->create([
                    'user_id'      => Auth::id(),
                    'type'         => $after > $before ? 'in' : 'out',
                    'quantity'     => abs($after - $before),
                    'stock_before' => $before,
                    'stock_after'  => $after,
                    'notes'        => 'Ajuste manual',
                ]);
            }
        });
    }
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn() => Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/' . $this->image));
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function kardexPrices(): HasMany
    {
        return $this->hasMany(KardexPrice::class);
    }
    public function kardexInventory(): HasMany
    {
        return $this->hasMany(KardexInventory::class);
    }
}
