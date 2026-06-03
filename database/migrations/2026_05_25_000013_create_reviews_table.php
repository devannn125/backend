<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id_review', 100)->primary();
            $table->string('id_user', 100);
            $table->string('id_hotel', 100);
            $table->unsignedTinyInteger('rating');
            $table->text('komentar')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            $table->foreign('id_hotel')->references('id_hotel')->on('hotels')->cascadeOnDelete();
            $table->unique(['id_user', 'id_hotel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
