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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio',20)->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('email');
            $table->decimal('subtotal',12,2);
            $table->decimal('taxes',12,2);
            $table->decimal('total',12,2);
            $table->decimal('exchange_rate',10,4);
            $table->string('status',20)->default('pending')->index();
            $table->string('payment_reference')->nullable();
            $table->dateTime('ordered_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
