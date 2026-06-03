<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id_booking', 100)->primary();
            $table->string('id_user', 100);
            $table->dateTime('tanggal_booking');
            $table->date('check_in');
            $table->date('check_out');
            $table->decimal('total_harga', 12, 0);
            $table->enum('status', ['success', 'pending', 'cancel'])->default('pending');

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
