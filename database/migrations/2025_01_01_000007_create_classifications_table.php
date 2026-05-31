<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('recommended_path'); // IPA, IPS, Bahasa, Vokasi
            $table->decimal('ipa_probability', 8, 4)->default(0);
            $table->decimal('ips_probability', 8, 4)->default(0);
            $table->decimal('bahasa_probability', 8, 4)->default(0);
            $table->decimal('vokasi_probability', 8, 4)->default(0);
            $table->text('dominant_factor')->nullable();
            $table->string('model_version')->nullable();
            $table->timestamp('classified_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
