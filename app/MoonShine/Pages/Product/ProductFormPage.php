<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Product;

use App\Models\Variation;
use App\MoonShine\Resources\VariationResource;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Divider;
use MoonShine\Fields\ID;
use MoonShine\Fields\Position;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\TypeCasts\ModelCast;

class ProductFormPage extends FormPage
{
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
                Text::make('Название', 'name')
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
            ]);
        }

        return $fields;
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
