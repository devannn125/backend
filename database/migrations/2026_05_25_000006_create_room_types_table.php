<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->string('id_room_type', 100)->primary();
            $table->string('nama_type', 50)->unique();
            $table->unsignedInteger('kapasitas');
            $table->decimal('harga_per_malam', 12, 0);
            $table->text('deskripsi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
