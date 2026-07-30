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
        Schema::table('loans', function (Blueprint $table) {
            // Ubah kolom payment_status agar nullable (boleh NULL)
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Kembalikan ke NOT NULL
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->nullable(false)->change();
        });
    }
};