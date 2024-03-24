<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Variation extends Model
{
    use HasFactory;

    protected static function booted() : void
    {
        static::created(function(Variation $variation) {
            $component = Component::query()->create([
                'name' => Component::SUMMARY_COMPONENT_NAME,
                'is_summary' => true,
                'variation_id' => $variation->id,
            ]);

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
        'product_id',
        'group_by'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function characteristics() : BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class)
            ->withPivot('group_order')
            ->using(CharacteristicVariation::class);
    }
}
