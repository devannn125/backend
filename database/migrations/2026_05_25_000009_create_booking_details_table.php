<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->string('id_booking_detail', 100)->primary();
            $table->string('id_booking', 100);
            $table->string('id_room', 100);
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('jumlah_malam');
            $table->decimal('subtotal', 12, 2);
            $table->enum('status', ['success', 'pending', 'cancel'])->default('pending');

            $table->foreign('id_booking')->references('id_booking')->on('bookings')->cascadeOnDelete();
            $table->foreign('id_room')->references('id_room')->on('rooms')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
