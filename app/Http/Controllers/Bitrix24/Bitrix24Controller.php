<?php

namespace App\Http\Controllers\Bitrix24;

use App\Http\Controllers\Controller;
use App\Services\Bitrix24\CRest;
use Illuminate\Contracts\View\View;

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
        $installedApp = CRest::installApp();

        return view('bitrix24.install', compact('installedApp'));
    }
}
