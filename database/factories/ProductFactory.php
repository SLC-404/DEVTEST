<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected static int $sku = 0;
    public function definition(): array
    {
        $priceUsd = fake()->randomFloat(2, 5, 150);
        return [
            'category_id'       => Category::factory(),
            'sku' => 'SKU-' . str_pad(++static::$sku, 4, '0', STR_PAD_LEFT),
            'name'              => ucwords(fake()->unique()->word(2, true)),
            'short_description' => fake()->sentence(10),
            'long_description'  => fake()->paragraphs(3, true),
            'cost_usd'          => round($priceUsd * fake()->randomFloat(2, 0.4, 0.7), 2),
            'price_usd'         => $priceUsd,
            'price_mxn'         => round($priceUsd * 18.50, 2),
            'image'             => 'https://picsum.photos/seed/' . fake()->unique()->uuid() . '/600/600',
            'stock'             => fake()->numberBetween(1, 100),
            'valid_until'       => fake()->dateTimeBetween('+1 month', '+1 year'),
            'is_active'         => true,
        ];
    }
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(['valid_until' => now()->subDays(5)]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }
}
