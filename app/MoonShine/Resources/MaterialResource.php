<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Resources\ModelResource;

class MaterialResource extends ModelResource
{
    protected string $model = Material::class;

    protected string $title = 'Materials';
    protected string $column = 'name';

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
