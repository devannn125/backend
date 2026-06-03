<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id_payment', 100)->primary();
            $table->string('id_booking', 100);
            $table->enum('metode_pembayaran', ['ewallet', 'creadit card', 'virtual account']);
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('status_pembayaran', ['success', 'pending', 'cancel'])->default('pending');
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('id_booking')->references('id_booking')->on('bookings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
