<?php

namespace App\Models;

use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Characteristic extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'id',
        'name',
        'type',
        'slug',
        'product_id'
    ];

    public function orders() : Attribute
    {
        return Attribute::make(
            get: function() {
                $orders = collect();

                foreach($this->variations as $variation) {
                    $orders[$variation->pivot->group_order] = $variation->pivot;
                }

                $orders = $orders->sortKeys();

                return $orders;
            }
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variations() : BelongsToMany
    {
        return $this->belongsToMany(Variation::class)->withPivot('group_order');
    }

    public function catalogs() : BelongsToMany
    {
        return $this->belongsToMany(Catalog::class)->withPivot('value');
    }
}
