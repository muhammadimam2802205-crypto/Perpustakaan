<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('book_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // petugas/admin
            $table->text('description')->nullable(); // deskripsi kerusakan
            $table->date('repair_date'); // tanggal perbaikan dimulai
            $table->date('deadline_date'); // batas waktu perbaikan (7 hari dari repair_date)
            $table->date('completion_date')->nullable(); // tanggal selesai perbaikan
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'terlambat'])->default('menunggu');
            $table->bigInteger('fine_amount')->default(0); // denda keterlambatan
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->nullable();
            $table->text('notes')->nullable(); // catatan tambahan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_repairs');
    }
};