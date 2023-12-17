<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Parameter;

use MoonShine\Pages\Crud\IndexPage;

class ParameterIndexPage extends IndexPage
{
    public function fields(): array
    {
        return [];
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
