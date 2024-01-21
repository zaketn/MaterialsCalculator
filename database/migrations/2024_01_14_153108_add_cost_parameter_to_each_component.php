<?php

use App\Models\Component;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $componentWithoutPrice = Component::query()->whereDoesntHave('parameters', function (Builder $query) {
            $query->where('name', Parameter::SUMMARY_PARAMETER_NAME);
        })
            ->get();

        foreach ($componentWithoutPrice as $component) {
            Parameter::query()->create([
                'name' => Parameter::SUMMARY_PARAMETER_NAME,
                'component_id' => $component->id,
                'slug' => Str::slug(Parameter::SUMMARY_PARAMETER_NAME),
                'formula' => json_encode([])
            ]);
        }
    }

    public function down(): void
    {
        Parameter::query()->has('component')
            ->where('name', Parameter::SUMMARY_PARAMETER_NAME)
            ->delete();
    }
};
