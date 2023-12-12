<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Variation;

use App\Models\Product;
use App\MoonShine\Resources\ProductResource;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;

class VariationFormPage extends FormPage
{
    public function fields(): array
    {
        return [
            Text::make('Название', 'name'),

            BelongsTo::make(
                'Продукт',
                'product',
                resource: new ProductResource()
            )
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
