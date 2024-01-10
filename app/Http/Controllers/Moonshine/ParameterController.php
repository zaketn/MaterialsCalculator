<?php

namespace App\Http\Controllers\Moonshine;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use Illuminate\Http\RedirectResponse;

class ParameterController extends Controller
{
    public function delete(string $id): RedirectResponse
    {
        Parameter::query()
            ->firstWhere('id', (int)$id)
            ->delete();

        return redirect()->back();
    }
}
