<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Resources\ModelResource;

class ServiceResource extends ModelResource
{
    protected string $model = Service::class;

    protected string $title = 'Services';

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
