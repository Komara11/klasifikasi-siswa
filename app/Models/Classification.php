<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classification extends Model
{
    protected $fillable = [
        'student_id', 'recommended_path', 'vocational_major',
        'ipa_probability', 'ips_probability', 'bahasa_probability', 'vokasi_probability',
        'vocational_probabilities',
        'dominant_factor', 'model_version', 'classified_at',
    ];

    protected $casts = [
        'classified_at' => 'datetime',
        'ipa_probability' => 'float',
        'ips_probability' => 'float',
        'bahasa_probability' => 'float',
        'vokasi_probability' => 'float',
        'vocational_probabilities' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
