<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class Formula extends MoonShineComponent
{
    protected string $view = 'admin.components.formula';

    public function __construct()
    {
        //
    }

    protected function viewData(): array
    {
        return [];
    }
}
