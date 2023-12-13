<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Variation;

use App\Models\Product;
use App\MoonShine\Resources\ProductResource;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;

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

        $this->relatedProduct = !empty(request()->get('relatedProductId'))
            ? Product::query()->findOrFail((int)request()->get('relatedProductId'))
            : null;
    }

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();
        $productResource = new ProductResource();

        array_shift($breadcrumbs);
        return [
                $productResource->url() => $productResource->title()
            ]
            + [
                $productResource->formPage()->route() => $this->relatedProduct->name
                    ?? $this->getResource()->getItem()->product->name
            ]
            + $breadcrumbs;
    }

    public function fields(): array
    {
        return [
            Text::make('Название', 'name'),

            BelongsTo::make(
                'Продукт',
                'product',
                resource: new ProductResource()
            )
                ->default($this->relatedProduct)
                ->disabled()
        ];
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
