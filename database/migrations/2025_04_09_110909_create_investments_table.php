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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con el usuario (inversor)
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade'); // Relación con la campaña
            $table->integer('amount'); // Monto invertido
            $table->decimal('interest_rate', 5, 2)->default(0.00); // Valor predeterminado de la tasa de interés
            $table->enum('status', ['reserved', 'active', 'cancelled'])->default('reserved'); // Columna de estatus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
