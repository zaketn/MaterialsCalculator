<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'unit',
        'price',
        'in_stock',
        'reserved',
        'shipped'
    ];

    public function type() : BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }
}
