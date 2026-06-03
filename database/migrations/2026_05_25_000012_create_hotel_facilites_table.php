<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_facilities', function (Blueprint $table) {
            $table->string('id_hotel', 100);
            $table->string('id_facility', 100);

            $table->primary(['id_hotel', 'id_facility']);
            $table->foreign('id_hotel')->references('id_hotel')->on('hotels')->cascadeOnDelete();
            $table->foreign('id_facility')->references('id_facility')->on('facilities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_facilities');
    }
};
