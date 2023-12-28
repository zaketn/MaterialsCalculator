<?php

declare(strict_types=1);

namespace App\MoonShine;

use MoonShine\Components\Layout\{Content,
    Flash,
    Footer,
    Header,
    LayoutBlock,
    LayoutBuilder,
    Menu,
    Profile,
    Search,
    Sidebar};
use MoonShine\Components\When;
use MoonShine\Contracts\MoonShineLayoutContract;

final class MoonShineLayout implements MoonShineLayoutContract
{
    public static function build(): LayoutBuilder
    {
        $developerName = config('moonshine.developer_name');
        $developerDescription = config('moonshine.developer_description');
        $developerSite = config('moonshine.developer_site');

        $footerText = "
                        $developerDescription
                        <a href=\"$developerSite\"
                            class=\"font-semibold text-primary hover:text-secondary\"
                            target=\"_blank\"
                        >
                            $developerName
                        </a>
                    ";

        return LayoutBuilder::make([
            Sidebar::make([
                Menu::make()->customAttributes(['class' => 'mt-2']),
                When::make(
                    static fn() => config('moonshine.auth.enable', true),
                    static fn() => [Profile::make(withBorder: true)]
                ),
            ]),

            LayoutBlock::make([
                Flash::make(),
                Header::make([
                    Search::make(),
                ]),
                Content::make(),
                Footer::make()->copyright(function() use ($footerText) {
                    return $footerText;
                }),
            ])->customAttributes(['class' => 'layout-page']),
        ]);
    }
}
