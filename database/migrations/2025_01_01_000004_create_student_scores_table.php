<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('semester'); // 1-5
            $table->decimal('score', 5, 2);
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scores');
    }
};
