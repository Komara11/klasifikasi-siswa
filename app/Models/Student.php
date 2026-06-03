<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = ['nis', 'name', 'gender', 'classroom_id', 'birth_date', 'address', 'photo'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StudentScore::class);
    }

    public function questionnaireAnswers(): HasMany
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }

    public function classification(): HasOne
    {
        return $this->hasOne(Classification::class)->latestOfMany();
    }

    public function classifications(): HasMany
    {
        return $this->hasMany(Classification::class);
    }

    /**
     * Check if student has complete academic scores.
     * Requires at least 6 subjects with scores.
     */
    public function hasCompleteScores(): bool
    {
        return $this->scores()->where('semester', 1)->count() >= 6;
    }

    /**
     * Check if student has completed all questionnaire answers.
     */
    public function hasCompleteQuestionnaire(): bool
    {
        $answerCount = $this->questionnaireAnswers()->count();
        $totalQuestions = QuestionnaireQuestion::count();
        return $answerCount >= $totalQuestions && $totalQuestions > 0;
    }

    /**
     * Check if student has complete data (scores + questionnaire answers).
     */
    public function getIsCompleteAttribute(): bool
    {
        return $this->hasCompleteScores() && $this->hasCompleteQuestionnaire();
    }

    /**
     * Get granular data completeness status.
     * 
     * Returns one of 4 statuses:
     * - "Sudah Diklasifikasi" — has classification result
     * - "Siap Diklasifikasi" — scores + questionnaire complete, not yet classified
     * - "Kuesioner Belum Lengkap" — scores OK, questionnaire missing
     * - "Nilai Belum Lengkap" — scores missing (may also have missing questionnaire)
     */
    public function getDataStatusAttribute(): string
    {
        $hasScores = $this->hasCompleteScores();
        $hasQuestionnaire = $this->hasCompleteQuestionnaire();
        $hasClassification = $this->classification !== null;

        if ($hasClassification) {
            return 'Sudah Diklasifikasi';
        }

        if ($hasScores && $hasQuestionnaire) {
            return 'Siap Diklasifikasi';
        }

        if ($hasScores && !$hasQuestionnaire) {
            return 'Kuesioner Belum Lengkap';
        }

        return 'Nilai Belum Lengkap';
    }

    /**
     * Get data status code for filtering/styling.
     */
    public function getDataStatusCodeAttribute(): string
    {
        return match($this->data_status) {
            'Sudah Diklasifikasi' => 'classified',
            'Siap Diklasifikasi' => 'ready',
            'Kuesioner Belum Lengkap' => 'no_questionnaire',
            'Nilai Belum Lengkap' => 'no_scores',
            default => 'unknown',
        };
    }

    /**
     * Get status label (backward compatibility).
     */
    public function getStatusAttribute(): string
    {
        return $this->is_complete ? 'Lengkap' : 'Belum Lengkap';
    }
}
