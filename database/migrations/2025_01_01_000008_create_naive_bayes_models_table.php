<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('naive_bayes_models', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');      // IPA, IPS, Bahasa, Vokasi
            $table->string('feature_name');     // e.g. matematika, ipa, minat_ipa, etc.
            $table->decimal('mean', 10, 4)->default(0);
            $table->decimal('variance', 10, 4)->default(0);
            $table->decimal('prior_probability', 8, 4)->default(0);
            $table->string('model_version');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('naive_bayes_models');
    }
};
