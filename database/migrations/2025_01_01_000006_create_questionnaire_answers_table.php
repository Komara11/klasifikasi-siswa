<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questionnaire_questions')->onDelete('cascade');
            $table->integer('score'); // Likert 1-5
            $table->timestamps();

            $table->unique(['student_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
