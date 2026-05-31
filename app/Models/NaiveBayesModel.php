<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NaiveBayesModel extends Model
{
    protected $fillable = ['class_name', 'feature_name', 'mean', 'variance', 'prior_probability', 'model_version'];

    protected $casts = [
        'mean' => 'float',
        'variance' => 'float',
        'prior_probability' => 'float',
    ];
}
