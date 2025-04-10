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
        Schema::create('amortization_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('payment_number');
            $table->date('payment_date');
            $table->decimal('interest_payment', 15, 2);
            $table->decimal('principal_payment', 15, 2);
            $table->decimal('outstanding_principal', 15, 2);
            $table->decimal('total_payment', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amortization_schedules');
    }
};
