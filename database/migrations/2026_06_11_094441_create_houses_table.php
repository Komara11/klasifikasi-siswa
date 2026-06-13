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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->integer('luas_tanah');
            $table->integer('luas_bangunan');
            $table->integer('kamar_tidur');
            $table->integer('kamar_mandi');
            $table->string('kecamatan');
            $table->double('jarak_kota'); // Jarak ke pusat kota (km)
            $table->integer('kondisi'); // 1-5
            $table->boolean('garasi')->default(false);
            $table->boolean('taman')->default(false);
            $table->boolean('carport')->default(false);
            $table->bigInteger('harga_aktual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
