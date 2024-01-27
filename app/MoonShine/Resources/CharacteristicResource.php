<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Resources\ModelResource;

class CharacteristicResource extends ModelResource
{
    protected string $model = Characteristic::class;

    protected string $title = 'Characteristics';
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
