<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Pages\Component\ComponentFormPage;
use App\MoonShine\Resources\ComponentResource;
use App\MoonShine\Resources\MaterialResource;
use App\MoonShine\Resources\MaterialTypeResource;
use App\MoonShine\Resources\ParameterResource;
use App\MoonShine\Resources\ProductResource;
use App\MoonShine\Resources\ServiceResource;
use App\MoonShine\Resources\VariationResource;
use App\MoonShine\Resources\WorkResource;
use Illuminate\Support\Facades\Vite;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Providers\MoonShineApplicationServiceProvider;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        moonshineAssets()->add([
            Vite::asset('resources/css/app.css'),
        ]);
    }

    protected function resources(): array
    {
        return [
            new VariationResource(),
            new ComponentResource(),
            new ParameterResource(),
            new MaterialResource(),
            new ServiceResource()
        ];
    }

    protected function pages(): array
    {
        return [
            ComponentFormPage::make()
        ];
    }

    protected function menu(): array
    {
        return [
            MenuItem::make('Продукты', new ProductResource()),

            MenuItem::make('Материалы', new MaterialTypeResource()),

            MenuItem::make('Работы', new WorkResource()),

            MenuGroup::make(static fn() => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.admins_title'),
                    new MoonShineUserResource()
                ),
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.role_title'),
                    new MoonShineUserRoleResource()
                ),
            ]),
        ];
    }

    /**
     * @return array{css: string, colors: array, darkColors: array}
     */
    protected function theme(): array
    {
        return [];
    }
}
