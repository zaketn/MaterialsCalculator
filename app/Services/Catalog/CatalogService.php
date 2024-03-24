<?php

namespace App\Services\Catalog;

use App\Models\Catalog;
use Illuminate\Support\Carbon;

class CatalogService
{
    public static function addRecord(string $name, int $productId, int $variationId, array $userInputs) : bool
    {
        $catalog = Catalog::query()->create([
            'name' => $name,
            'product_id' => $productId,
            'variation_id' => $variationId,
        ]);

        foreach ($userInputs as $userInput) {
            $catalog->characteristics()
                ->attach([
                    $userInput['id'] => [
                        'value' => class_exists($userInput['type']) ? $userInput['modelId'] : $userInput['value'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                ]);
        }

        return true;
    }
}
