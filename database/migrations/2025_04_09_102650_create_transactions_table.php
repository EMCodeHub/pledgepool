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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
        $table->foreignId('investment_account_id')->constrained()->onDelete('cascade'); // Relación con la cuenta de inversión
        $table->decimal('amount', 10, 2); // Esto asegura que los montos se guarden como decimales

        $table->enum('transaction_type', ['deposit', 'withdrawal']); // Tipo de transacción
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
