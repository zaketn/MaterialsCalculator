<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Work;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Enums\ClickAction;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class WorkResource extends ModelResource
{
    protected string $model = Work::class;

    protected string $title = 'Работы';

    protected string $column = 'name';

    protected array $with = ['services'];

    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),

                Text::make('Название', 'name'),

                Json::make('Услуги', 'services')
                    ->asRelation(new ServiceResource())
                    ->fields([
                        ID::make(),
                        Text::make('Название', 'name'),
                        Select::make('Единица измерения', 'unit')
                            ->options([
                                'oper' => 'опер.'
                            ]),
                        Number::make('Коэффициент сложности', 'difficult_coef')
                            ->step(0.1)
                            ->buttons(),
                        Number::make('Цена', 'price')
                            ->step(1)
                            ->buttons(),
                    ])
                    ->hideOnIndex()
                    ->removable()
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
