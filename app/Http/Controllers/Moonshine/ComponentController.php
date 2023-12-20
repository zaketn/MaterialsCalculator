<?php

namespace App\Http\Controllers\Moonshine;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function __invoke(Request $request)
    {
        $component = Component::query()->find((int)$request->post('id'));

        $component->update([
            'name' => $request->post('name')
        ]);

        foreach ($request->post() as $name => $updatedParameters) {
            $namesToSkip = ['_token', 'id', 'name'];
            if (array_search($name, $namesToSkip) !== false) continue;

            $component->parameters()->updateOrCreate(
                [
                    'slug' => $name
                ],
                [
                    'name' => $updatedParameters['name'],
                    'formula' => $updatedParameters['formula']
                ]
            );
        }

        return redirect()->back();
    }
}
