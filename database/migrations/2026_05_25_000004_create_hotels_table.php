<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->string('id_hotel', 100)->primary();
            $table->string('nama_hotel', 100);
            $table->text('alamat');
            $table->string('kota', 50);
            $table->text('deskripsi')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('hotel_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
