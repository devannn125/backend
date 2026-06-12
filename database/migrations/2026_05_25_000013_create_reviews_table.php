<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Reviews Utama
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id_review', 100)->primary();
            $table->string('id_user', 100);
            $table->string('id_booking', 100)->unique(); // Kunci: 1 Booking = 1 Review
            $table->string('id_hotel', 100);
            $table->unsignedTinyInteger('rating');
            $table->text('komentar')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            $table->foreign('id_booking')->references('id_booking')->on('bookings')->cascadeOnDelete();
            $table->foreign('id_hotel')->references('id_hotel')->on('hotels')->cascadeOnDelete();
        });

        // 2. Tabel Review Media (Untuk menampung banyak foto/video)
        Schema::create('review_media', function (Blueprint $table) {
            $table->id();
            $table->string('id_review', 100);
            $table->string('media_path');
            $table->timestamps();

            $table->foreign('id_review')->references('id_review')->on('reviews')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_media');
        Schema::dropIfExists('reviews');
    }
};
