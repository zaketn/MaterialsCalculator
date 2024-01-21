<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Component extends Model
{
    use HasFactory;

    protected static function booted() : void
    {
        static::created(function(Component $component) {
            Parameter::query()->create([
                'name' => Parameter::SUMMARY_PARAMETER_NAME,
                'component_id' => $component->id,
                'slug' => Str::slug(Parameter::SUMMARY_PARAMETER_NAME),
                'formula' => json_encode([])
            ]);
        });
    }

    protected $fillable = [
        'id',
        'name',
        'variation_id'
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
