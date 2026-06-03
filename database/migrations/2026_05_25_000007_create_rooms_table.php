<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->string('id_room', 100)->primary();
            $table->string('id_hotel', 100);
            $table->string('id_room_type', 100);
            $table->string('room_image')->nullable();
            $table->string('nomor_kamar', 10);
            $table->enum('status', ['available', 'booked', 'maintenance'])->default('available');

            $table->foreign('id_hotel')->references('id_hotel')->on('hotels')->cascadeOnDelete();
            $table->foreign('id_room_type')->references('id_room_type')->on('room_types')->restrictOnDelete();
            $table->unique(['id_hotel', 'nomor_kamar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
