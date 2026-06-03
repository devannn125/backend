<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings_addons', function (Blueprint $table) {
            $table->string('id_booking_addon', 100)->primary();
            $table->string('id_booking', 100);
            $table->string('id_addon', 100);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 0);
            $table->text('catatan')->nullable();

            $table->foreign('id_booking')->references('id_booking')->on('bookings')->cascadeOnDelete();
            $table->foreign('id_addon')->references('id_addon')->on('addons')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings_addons');
    }
};
