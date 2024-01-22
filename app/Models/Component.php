<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    use HasFactory;

    const SUMMARY_COMPONENT_NAME = 'Итоговая стоимость';

    protected $fillable = [
        'id',
        'name',
        'is_summary',
        'variation_id'
    ];

    protected $casts = [
        'is_summary' => 'bool'
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class);
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class);
    }
}
