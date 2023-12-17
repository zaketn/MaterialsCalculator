<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Component;

use App\Models\Variation;
use App\MoonShine\Components\Formula;
use App\MoonShine\Resources\ProductResource;
use App\MoonShine\Resources\VariationResource;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;

class ComponentFormPage extends FormPage
{
    protected ?Variation $relatedVariation;

    public function __construct(
        ?string           $title = null,
        ?string           $alias = null,
        ?ResourceContract $resource = null
    )
    {
        parent::__construct($title, $alias, $resource);

        $relatedVariationId = request()->get('relatedVariationId');
        $this->relatedVariation = !empty($relatedVariationId)
            ? Variation::query()->findOrFail((int)$relatedVariationId)
            : null;

    }

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();
        $productResource = new ProductResource();
        $variationResource = new VariationResource();

        array_shift($breadcrumbs);

        if(!$this->getResource()->getItem())
        {
            $breadcrumbs[array_keys($breadcrumbs)[0]] .= ' компонент вариации';
        }
        return [
                $productResource->url() => $productResource->title()
            ]
            + [
                $productResource->formPage()->route() => $this->getResource()->getItem()
                    ? $this->getResource()->getItem()->variation->product->name
                    : $this->relatedVariation->product->name
            ]
            + [
                $variationResource->formPage()->route() => $this->getResource()->getItem()
                    ? $this->getResource()->getItem()->variation->name
                    : $this->relatedVariation->name
            ]
            + $breadcrumbs;
    }

    public function fields(): array
    {
        $fields = [
            Text::make('Название', 'name'),

            // TODO: заблокировать возможность выбора
            BelongsTo::make('Вариация продукта',
                'variation',
                resource: new VariationResource()
            )
                ->default($this->relatedVariation),

            Divider::make(),

            Heading::make('Рассчётные параметры')
        ];

        foreach($this->getResource()->getItem()->parameters as $parameter){
            $fields[] = Collapse::make($parameter->name, [
                Text::make('Название', 'name', fn($parameter) => $parameter->name),
                Formula::make()
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
