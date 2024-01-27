<?php

namespace App\Console\Commands\Convert;

use App\Models\Characteristic;
use App\Models\Product;
use Illuminate\Console\Command;

class CharacteristicsJsonToModel extends Command
{
    protected $signature = 'app:convert-characteristics';

    protected $description = 'Переносит характеристики продуктов из JSON поля в отдельную модель.';

    public function handle()
    {
        $products = Product::all();

        foreach($products as $product) {
            foreach($product->characteristics as $characteristic) {
                if(!empty($characteristic['name'])) {
                    Characteristic::query()->create([
                        'name' => $characteristic['name'],
                        'type' => $characteristic['type'],
                        'slug' => $characteristic['slug'] ?? '',
                        'product_id' => $product->id
                    ]);
                }
            }
        }
    }
}
