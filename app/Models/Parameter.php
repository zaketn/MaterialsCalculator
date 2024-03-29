<?php

namespace App\Models;

use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parameter extends Model
{
    use HasFactory, HasSlug;

    const SUMMARY_PARAMETER_NAME = 'Формула';

    protected $fillable = [
        'name',
        'component_id',
        'slug',
        'formula'
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
