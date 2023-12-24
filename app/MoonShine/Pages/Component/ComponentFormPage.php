<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Component;

use App\Models\Component;
use App\Models\Variation;
use App\MoonShine\Components\Formula;
use App\MoonShine\Resources\ProductResource;
use App\MoonShine\Resources\VariationResource;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Divider;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;

class ComponentFormPage extends FormPage
{
    protected ?Variation $relatedVariation;
    protected ?Component $component;

    public function __construct(
        ?string           $title = null,
        ?string           $alias = null,
        ?ResourceContract $resource = null,
    )
    {
        parent::__construct($title, $alias, $resource);

        $this->component = Component::query()
            ->find(request()->get('resourceItem'));

        $this->relatedVariation = $this->getRelatedVariation();
    }

    public function components(): array
    {
        $fields = [
            Text::make('ID', 'id')
                ->fill($this->getResource()->getItem()->id)
                ->setAttribute('type', 'hidden'),

            Text::make('Название', 'name')
                ->default($this->component->name)
                ->setAttribute('style', 'margin-bottom: 1.5rem'),

            ActionButton::make('Добавить параметр')
                ->inOffCanvas(
                    fn() => 'Добавить параметр',
                    fn() => form()->fields(
                        [
                            Text::make('', 'component_id')
                                ->setAttribute('type', 'hidden')
                                ->fill($this->getResource()->getItem()->id),

                            Text::make('Название', 'name')
                        ]
                    )
                    ->action(route('component.store'))
                ),

            Divider::make()
        ];

        if (!empty($this->getResource()->getItem()->parameters)) {
            foreach ($this->getResource()->getItem()->parameters as $parameter) {
                $fields[] = Collapse::make($parameter->name, [
                    Text::make('Название параметра', $parameter->slug . '[name]')
                        ->fill($parameter->name),
                    Formula::make($this->getResource()->getItem(), $parameter)
                ]);
            }
        }

        return [
            FormBuilder::make(route('component.save'), 'POST', $fields)
        ];
    }

    public function breadcrumbs(): array
    {
        $breadcrumbs = parent::breadcrumbs();
        $productResource = new ProductResource();
        $variationResource = new VariationResource();
        $relatedVariation = $this->getResource()->getItem()->variation;

        array_shift($breadcrumbs);

        if (!$this->getResource()->getItem()) {
            $breadcrumbs[array_keys($breadcrumbs)[0]] .= ' компонент вариации';
        }
        return [
                $productResource->url() => $productResource->title()
            ]
            + [
                $productResource->formPage()->route() => $this->getResource()->getItem()
                    ? $relatedVariation->product->name
                    : $this->relatedVariation->product->name
            ]
            + [
                $variationResource->formPage()->route(['resourceItem' => $relatedVariation->id]) => $this->getResource()->getItem()
                    ? $relatedVariation->name
                    : $this->relatedVariation->name
            ]
            + $breadcrumbs;
    }

    private function getRelatedVariation(): ?Variation
    {
        $relatedVariationId = request()->get('relatedVariationId');
        return !empty($relatedVariationId)
            ? Variation::query()->find((int)$relatedVariationId)
            : null;
    }
}
