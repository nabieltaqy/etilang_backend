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
        Schema::create('violations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number');
            // $table->unsignedBigInteger('vehicle_id');
            // $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('camera_id');
            $table->foreign('camera_id')->references('id')->on('cameras')->onDelete('cascade');
            $table->string('evidence');
            $table->unsignedBigInteger('violation_type_id')->default(1);
            $table->foreign('violation_type_id')->references('id')->on('violation_types')->onDelete('cascade');
            $table->enum('status', ['Terdeteksi', 'Tilang', 'Batal'])->default('Terdeteksi');
            $table->string('cancel_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
