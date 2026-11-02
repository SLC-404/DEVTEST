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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->char('base_currency',3)->default('USD');
            $table->char('quote_currency',3)->default('MXN');
            $table->decimal('rate',10,4);
            $table->string('source',50)->default('banxico');
            $table->timestamps();

            $table->unique(['date','base_currency','quote_currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
