<?php

namespace App\Http\Controllers\Moonshine;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComponentController extends Controller
{
    public function save(Request $request)
    {
        $component = Component::query()->updateOrCreate(
            [
                'id' => (int)$request->post('id')
            ],
            [
                'name' => $request->post('name'),
                'variation_id' => $request->post('variation_id')
            ]
        );

        foreach ($request->post() as $name => $updatedParameters) {
            $namesToSkip = ['_token', 'id', 'name', 'variation_id'];
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

    public function store(Request $request)
    {
        Parameter::query()->create([
            'name' => $request->post('name'),
            'formula' => '[]',
            'slug' => Str::slug($request->post('name')),
            'component_id' => $request->post('component_id')
        ]);

        return redirect()->back();
    }
}
