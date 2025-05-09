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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('violation_id');
            $table->foreign('violation_id')->references('id')->on('violations')->onDelete('cascade');
            $table->unsignedBigInteger('investigator_id')->nullable();
            $table->foreign('investigator_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('color', ['red', 'blue'])->nullable();
            $table->enum('status', ['Tilang', 'Himbauan', 'Persidangan', 'Sudah Bayar', 'Lewat Tenggat'])->default('Tilang');
            $table->dateTime('deadline_confirmation');
            $table->unsignedBigInteger('hearing_schedule_id')->nullable();
            $table->foreign('hearing_schedule_id')->references('id')->on('hearing_schedules')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
