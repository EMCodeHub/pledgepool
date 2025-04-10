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
        Schema::create('investment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Relación con los usuarios
            $table->decimal('balance', 15, 2)->default(0); // Saldo de la cuenta
            $table->decimal('reserved_amount', 15, 2)->default(0); // Monto reservado
            $table->string('currency')->default('EUR'); // Agregamos la columna 'currency' con valor por defecto 'EUR'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_accounts');
    }
};
