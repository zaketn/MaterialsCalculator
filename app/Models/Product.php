<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'characteristics'
    ];

    protected $casts = [
        'characteristics' => 'array'
    ];

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }
}
