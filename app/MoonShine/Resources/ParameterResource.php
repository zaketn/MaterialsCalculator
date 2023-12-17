<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Parameter;
use App\MoonShine\Pages\Parameter\ParameterDetailPage;
use App\MoonShine\Pages\Parameter\ParameterFormPage;
use App\MoonShine\Pages\Parameter\ParameterIndexPage;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Resources\ModelResource;

class ParameterResource extends ModelResource
{
    protected string $model = Parameter::class;

    protected string $title = 'Parameters';

    public function pages(): array
    {
        return [
            ParameterIndexPage::make($this->title()),
            ParameterFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            ParameterDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
