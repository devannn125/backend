<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->string('id_addon', 100)->primary();
            $table->string('nama_addon', 100)->unique();
            $table->text('deskripsi');
            $table->decimal('harga', 12, 0);
            $table->enum('status', ['available', 'unavailable'])->default('available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
