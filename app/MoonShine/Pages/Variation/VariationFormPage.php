<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Variation;

use App\Models\Component;
use App\Models\Material;
use App\Models\Product;
use App\MoonShine\Resources\CharacteristicResource;
use App\MoonShine\Resources\ComponentResource;
use App\MoonShine\Resources\ProductResource;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Divider;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\TypeCasts\ModelCast;

class VariationFormPage extends FormPage
{
    private ?Product $relatedProduct;

    public function __construct(
        ?string           $title = null,
        ?string           $alias = null,
        ?ResourceContract $resource = null
    )
    {
        parent::__construct($title, $alias, $resource);

        $relatedProductId = request()->get('relatedProductId');
        $this->relatedProduct = !empty($relatedProductId)
            ? Product::query()->find((int)$relatedProductId)->with('characteristics')
            : null;
    }

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();
        $productResource = new ProductResource();

        array_shift($breadcrumbs);

        if (!$this->getResource()->getItem()) {
            $breadcrumbs[array_keys($breadcrumbs)[0]] .= ' вариацию';
        }
        return [
                $productResource->url() => $productResource->title()
            ]
            + [
                $productResource->formPage()->route(['resourceItem' =>
                    $this->relatedProduct->id ?? $this->getResource()->getItem()->product->id]) => $this->relatedProduct->name
                    ?? $this->getResource()->getItem()->product->name
            ]
            + $breadcrumbs;
    }

    public function fields(): array
    {
        $fields = [
            Block::make([
                Text::make('Название', 'name'),

                Collapse::make('Настройки', [
                    BelongsTo::make(
                        'Продукт',
                        'product',
                        resource: new ProductResource()
                    )
                        ->default($this->relatedProduct),

                    BelongsToMany::make(
                        'Характеристика для группировки',
                        'characteristics',
                        resource: new CharacteristicResource()
                    )
                        ->valuesQuery(
                            fn(Builder $query) => $query
                                ->where('product_id', $this->getResource()->getItem()->product->id ?? $this->relatedProduct->id)
                        )
                        ->fields([
                            Number::make('Очередь группировки', 'group_order')
                                ->min(1)
                                ->max(10)
                                ->step(1)
                                ->buttons()
                        ]),
                ])
            ]),
        ];

        if (!empty($this->getResource()->getItem())) {
            $fields[] = Divider::make();
            $fields[] = ActionButton::make('Добавить компонент вариации', function () {
                $variationFormPage = new ComponentResource();

                return $variationFormPage->formPage()
                    ->route([
                        'relatedVariationId' => $this->getResource()->getItemID()
                    ]);
            })
                ->customAttributes(['style' => 'margin-bottom: 1rem']);

            $fields[] = TableBuilder::make(items: $this->getResource()->getItem()->components)
                ->fields([
                    Text::make('Название', formatted: fn($item) => $item->name)
                ])
                ->cast(ModelCast::make(Component::class))
                ->buttons([
                    EditButton::for(new ComponentResource()),

                    DeleteButton::for(new ComponentResource())
                        ->canSee(fn($component) => !$component->is_summary)
                ])
                ->withNotFound();
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
