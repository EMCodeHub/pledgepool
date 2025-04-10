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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->integer('amount');
            $table->integer('contract_fee');
            $table->decimal('interest_rate', 5, 2);
            $table->enum('campaign_type', ['normal', 'auction']);
            $table->date('deadline');
            $table->integer('loan_duration');
            $table->integer('target_amount');
            $table->enum('status', ['active', 'closed', 'cancelled'])->default('active');
            $table->string('type')->nullable(); // Agregar la columna 'type'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
