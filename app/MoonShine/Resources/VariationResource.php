<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Variation;
use App\MoonShine\Pages\Variation\VariationIndexPage;
use App\MoonShine\Pages\Variation\VariationFormPage;
use App\MoonShine\Pages\Variation\VariationDetailPage;
use MoonShine\Resources\ModelResource;

class VariationResource extends ModelResource
{
    protected string $model = Variation::class;
    protected string $title = 'Вариации продуктов';
    protected string $column = 'name';
    public function redirectAfterDelete(): string
    {
        $productResource = new ProductResource();

        return $productResource->formPage()->route();
    }

    public function pages(): array
    {
        return [
            VariationIndexPage::make($this->title()),
            VariationFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            VariationDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
