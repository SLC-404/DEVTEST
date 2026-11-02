<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rate = (float) (ExchangeRate::latest('date')->value('rate') ?? 18.50);

        $mxn = fn (array $attributes) => [
            'price_mxn' => round($attributes['price_usd'] * $rate, 2),
        ];
        Category::all()->each(function (Category $category) use ($mxn) {
            Product::factory()->count(6)->for($category)->state($mxn)->create();
        });
        $category = Category::first();
        Product::factory()->count(2)->for($category)->inactive()->state($mxn)->create();
        Product::factory()->count(2)->for($category)->expired()->state($mxn)->create();
        Product::factory()->count(2)->for($category)->outOfStock()->state($mxn)->create();
    }
}
