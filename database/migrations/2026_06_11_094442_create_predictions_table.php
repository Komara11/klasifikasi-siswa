<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('luas_tanah');
            $table->integer('luas_bangunan');
            $table->integer('kamar_tidur');
            $table->integer('kamar_mandi');
            $table->string('kecamatan');
            $table->double('jarak_kota');
            $table->integer('kondisi');
            $table->boolean('garasi')->default(false);
            $table->boolean('taman')->default(false);
            $table->bigInteger('harga_prediksi');
            $table->string('kategori'); // Murah, Menengah, Mewah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
