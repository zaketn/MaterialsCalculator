<?php

namespace App\Http\Controllers\Bitrix24;

use App\Http\Controllers\Controller;
use App\Services\Bitrix24\CRest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class Bitrix24Controller extends Controller
{
    /**
     * Производит первоначальную установку при использовании в Bitrix24.
     * Размещает вкладку "Калькулятор" во вкладке сделки.
     *
     * @return View
     */
    public function __invoke(): View
    {
        $placement = CRest::call(
            'placement.bind',
            [
                'PLACEMENT' => 'CRM_DEAL_DETAIL_TAB',
                'HANDLER' => config('app.url'),
                'LANG_ALL' => [
                    'ru' => [
                        'TITLE' => 'Калькулятор',
                    ],
                ],
            ]
        );
        Log::debug($placement);

        $installedApp = CRest::installApp();

        return view('bitrix24.install', compact('installedApp', 'placement'));
    }
}
