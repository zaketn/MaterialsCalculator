<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'unit',
        'difficult_coef',
        'price',
    ];

    public function work(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
