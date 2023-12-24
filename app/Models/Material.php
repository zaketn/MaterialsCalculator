<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function type()
    {
        return $this->belongsTo(MaterialType::class);
    }
}
