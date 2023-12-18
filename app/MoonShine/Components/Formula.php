<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\Component;
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
     */
    public function __construct(
        protected Component $component
    )
    {
        $this->product = $this->component->variation->product;
    }

    protected function viewData(): array
    {
        return [
            'characteristics' => $this->product->characteristics
        ];
    }
}
