<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classifications', function (Blueprint $table) {
            $table->string('vocational_major')->nullable()->after('recommended_path');
            $table->json('vocational_probabilities')->nullable()->after('vokasi_probability');
        });
    }

    public function down(): void
    {
        Schema::table('classifications', function (Blueprint $table) {
            $table->dropColumn(['vocational_major', 'vocational_probabilities']);
        });
    }
};
