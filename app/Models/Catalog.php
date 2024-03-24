<?php

namespace App\Models;

use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Catalog extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'product_id',
        'variation_id'
    ];

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class)
            ->withPivot('value')
            ->using(CharacteristicVariation::class);
    }

    public function variation() : BelongsTo
    {
        return $this->belongsTo(Variation::class);
    }
}
