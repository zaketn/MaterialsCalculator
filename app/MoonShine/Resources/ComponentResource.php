<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Component;
use App\MoonShine\Pages\Component\ComponentDetailPage;
use App\MoonShine\Pages\Component\ComponentFormPage;
use App\MoonShine\Pages\Component\ComponentIndexPage;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Resources\ModelResource;

class ComponentResource extends ModelResource
{
    protected string $model = Component::class;

    protected string $title = 'Компоненты вариаций';

    protected string $column = 'name';

    protected array $with = ['parameters'];

    public function redirectAfterDelete(): string
    {
        $productResource = new VariationResource();

        return $productResource->formPage()->route();
    }

    public function pages(): array
    {
        return [
            ComponentIndexPage::make($this->title()),
            ComponentFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            ComponentDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
