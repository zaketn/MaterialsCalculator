<?php

use App\Models\Component;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Parameter::query()->has('component')
            ->where('name', 'Итоговая стоимость')
            ->delete();
    }

    public function down(): void
    {
        $componentWithoutPrice = Component::query()->whereDoesntHave('parameters', function (Builder $query) {
            $query->where('name', 'Итоговая стоимость');
        })
            ->get();

        foreach ($componentWithoutPrice as $component) {
            Parameter::query()->create([
                'name' => 'Итоговая стоимость',
                'component_id' => $component->id,
                'slug' => Str::slug('Итоговая стоимость'),
                'formula' => json_encode([])
            ]);
        }
    }
};
