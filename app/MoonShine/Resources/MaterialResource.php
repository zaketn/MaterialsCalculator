<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Position;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class MaterialResource extends ModelResource
{
    protected string $model = Material::class;

    protected string $title = 'Материалы';
    protected string $column = 'name';

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Название', 'name'),
                Json::make('Товары', 'items')
                    ->fields([
                        Position::make(),
                        Text::make('Название', 'name'),
                        Number::make('Цена', 'price'),
                        Select::make('Единица измерения', 'unit')
                            ->options([
                                'kg' => 'кг.',
                                'qm' => 'кв.м',
                                'pc' => 'шт.',
                                'pm.' => 'п.м',
                            ]),
                    ])
                ->removable()
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
