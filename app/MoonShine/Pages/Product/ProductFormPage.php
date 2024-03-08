<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Product;

use App\Models\Material;
use App\Models\Variation;
use App\MoonShine\Resources\CharacteristicResource;
use App\MoonShine\Resources\VariationResource;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Divider;
use MoonShine\Fields\ID;
use MoonShine\Fields\Json;
use MoonShine\Fields\Position;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\TypeCasts\ModelCast;

class ProductFormPage extends FormPage
{
    protected array $materials;

    public function __construct(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
    {
        parent::__construct($title, $alias, $resource);
    }

    public function title(): string
    {
        return !empty($this->getResource()->getItem())
            ? 'Редактировать продукт ' . '"' . $this->getResource()->getItem()->name . '"'
            : __('moonshine::ui.add');
    }

    public function fields(): array
    {
        $fields = [
            Block::make('Настройки продукта', [
                ID::make(),
                Text::make('Название', 'name'),
            ]),
        ];

        if (!empty($this->getResource()->getItem())) {
            $fields[] = Divider::make();

            $fields[] = ActionButton::make('Добавить вариацию продукта', function () {
                $variationFormPage = new VariationResource();

                return $variationFormPage->formPage()
                    ->route([
                        'relatedProductId' => $this->getResource()->getItemID()
                    ]);
            })
                ->customAttributes(['style' => 'margin-bottom: 1rem']);

            $fields[] = Block::make('Вариации продукта', [
                TableBuilder::make(items: $this->getResource()->getItem()->variations)
                    ->trAttributes(
                        // Если не заполнено поле для группировки то строка выделяется красным
                        function(mixed $data, int $row, ComponentAttributeBag $attributes): ComponentAttributeBag {
                            if($data->group_by === null) {
                                return $attributes->merge(['class' => 'bgc-red']);
                            }

                            return $attributes;
                        }
                    )
                    ->fields([
                        Position::make(),
                        Text::make('Название', formatted: fn($item) => $item->name)
                    ])
                    ->buttons([
                        EditButton::for(new VariationResource()),
                        DeleteButton::for(new VariationResource())
                    ])
                    ->cast(ModelCast::make(Variation::class))
                    ->withNotFound()
            ])
                ->customAttributes(['style' => 'margin-bottom: 1rem']);
        }

        $fields[] = Block::make('Характеристики', [
            Json::make('', 'characteristics')
                ->asRelation(new CharacteristicResource())
                ->fields([
                    ID::make(),
                    Text::make('Название', 'name'),
                    Select::make('Тип поля', 'type')
                        ->options([
                            'Общее' => [
                                'number' => 'Число',
                            ],
                            'Другое' => [
                                Material::class => 'Материал'
                            ]
                        ])
                ])
            ->removable()
        ]);

        return $fields;
    }
}
