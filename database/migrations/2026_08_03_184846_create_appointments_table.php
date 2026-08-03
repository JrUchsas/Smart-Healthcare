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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('appointment_slot_id')->constrained('appointment_slots')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->decimal('consultation_fee', 10, 2);
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->text('patient_notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index(['doctor_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
