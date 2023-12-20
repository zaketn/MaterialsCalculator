<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\Component;
use App\Models\Parameter;
use App\Models\Product;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class Formula extends MoonShineComponent
{
    protected string $view = 'admin.components.formula';
    protected Product $product;

    /**
     * @param Component $component
     * @param Parameter $parameter
     */
    public function __construct(
        protected Component $component,
        protected Parameter $parameter
    )
    {
        $this->product = $this->component->variation->product;
    }

    protected function viewData(): array
    {
        return [
            'parameter' => $this->parameter,
            'characteristics' => $this->product->characteristics
        ];
    }
}
