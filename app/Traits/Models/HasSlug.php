<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $model->slug = self::generateSlug($model);
        });
    }

    private static function generateSlug(Model $model, int $count = 0) : string
    {
        $sSlug = $model->slug ?? Str::slug($model->{self::slugField()}, '_');
        if($count > 0){
            $sSlug = $sSlug . '_' . $count;
        }

        $bIsSlugsDuplicates = $model->query()
            ->where('slug', $sSlug)
            ->count();

        if($bIsSlugsDuplicates){
            $count += 1;
            return self::generateSlug($model, $count);
        }

        return $sSlug;
    }

    private static function slugField() : string
    {
        return 'name';
    }
}
