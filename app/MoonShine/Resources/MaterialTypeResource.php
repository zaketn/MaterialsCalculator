<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Enums\ClickAction;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class MaterialTypeResource extends ModelResource
{
    protected string $model = MaterialType::class;

    protected string $title = 'Материалы';

    protected string $column = 'name';

    protected ?ClickAction $clickAction = ClickAction::EDIT;

    protected array $with = ['materials'];

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Название', 'name'),

                Json::make('Предметы', 'materials')
                    ->asRelation(new MaterialResource())
                    ->fields([
                        ID::make(),
                        Text::make('Название', 'name')->required(),
                        Select::make('Единица измерения', 'unit')
                            ->options([
                                'kg' => 'кг.'
                            ])
                            ->required(),
                        Number::make('Цена', 'price')
                            ->buttons()
                            ->step(0.1)
                            ->required(),
                        Number::make('В наличии', 'in_stock')
                            ->buttons()
                            ->required(),
                        Number::make('Зарезервировано', 'reserved')
                            ->buttons()
                            ->required(),
                        Number::make('Списано', 'shipped')
                            ->buttons()
                            ->required(),
                    ])
                    ->removable()
                    ->hideOnIndex()
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
