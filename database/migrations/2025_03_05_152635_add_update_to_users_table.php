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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->unique();
            $table->enum('role', ['admin', 'polisi'])->default('polisi');
            $table->boolean('is_2fa_enabled')->default(false); //indicator for 2FA
            $table->string('google2fa_secret')->nullable(); //store secret for 2FA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nip');
            $table->dropColumn('role');
            $table->dropColumn('is_2fa_enabled');
            $table->dropColumn('google2fa_secret');
        });
    }
};
