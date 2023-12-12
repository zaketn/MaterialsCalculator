<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Product;

use MoonShine\Components\TableBuilder;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\IndexPage;

class ProductIndexPage extends IndexPage
{
    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
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
