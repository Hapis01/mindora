<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'version',
        'algorithm',
        'training_date',
        'dataset_version',
        'metrics',
        'artifact_path',
        'status',
    ];

    protected $casts = [
        'training_date' => 'datetime',
        'metrics' => 'array',
    ];

    public function predictions(): HasMany
    {
        return $this->hasMany(MlPrediction::class, 'model_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
