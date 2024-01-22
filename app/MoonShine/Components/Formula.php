<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\Component;
use App\Models\Parameter;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use MoonShine\ActionButtons\ActionButton;
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
     * @param Collection $parameters
     * @param Parameter $parameter
     */
    public function __construct(
        protected Component $component,
        protected Collection $parameters,
        protected Parameter $parameter
    )
    {
        $this->product = $this->component->variation->product;
    }

    protected function viewData(): array
    {
        $viewData = [
            'parameters' => $this->parameters,
            'parameter' => $this->parameter,
            'characteristics' => $this->product->characteristics,
            'actionButtons' => [
                'deleteParameter' => ActionButton::make('Удалить параметр', route('parameter.delete', ['id' => $this->parameter->id]))
                    ->withConfirm('Удалить параметр', 'Вы действительно хотите удалить параметр?', 'Да', method: 'DELETE')
                    ->error()
                    ->render(),
            ],
            'isSummary' => $this->component->is_summary
        ];

        if($this->component->is_summary) {
            $viewData['allComponents'] = $this->component->variation->components()->with('parameters')->get();
        }

        return $viewData;
    }
}
