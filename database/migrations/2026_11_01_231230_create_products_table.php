<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('sku',50)->unique();
            $table->string('name',150);
            $table->string('short_description',255);
            $table->text('long_description');
            $table->decimal('cost_usd',10,2);
            $table->decimal('price_usd',10,2);
            $table->decimal('price_mxm',10,2);
            $table->string('image');
            $table->unsignedBigInteger('stock')->default(0);
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active','valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
