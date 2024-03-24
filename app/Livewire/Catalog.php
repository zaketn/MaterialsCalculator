<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

class Catalog extends CalculatableComponent
{
    public array $selectedCatalogs;
    protected array $calculatedProducts;


    #[Computed]
    public function products()
    {
        $characteristics = Product::query()
            ->with([
                'characteristics' => function(Builder $characteristic) {
                    return $characteristic->has('catalogs')->with(['catalogs.variation', 'catalogs.characteristics']);
                }
            ])
            ->whereHas('characteristics', function(Builder $characteristic) {
                return $characteristic->has('catalogs')->with('catalogs');
            })
        ->get();

        return $characteristics;
    }

    #[Computed]
    public function catalogs() : array
    {
        $catalogs = [];

        foreach($this->products as $product) {
            foreach($product->characteristics as $characteristic) {
                $catalogs[$product->id][] = $characteristic->catalogs;
            }

            $catalogs[$product->id] = collect($catalogs[$product->id])->flatten()->unique('slug');
        }

        return $catalogs;
    }

    public function sendOffer() : void
    {
        $this->selectedCatalogs = collect($this->selectedCatalogs)->map(function($catalogId) {
            $catalog = \App\Models\Catalog::query()
                ->with(['characteristics', 'variation.product'])
                ->firstWhere('id', $catalogId);

            $result = [];

            $result['selectedVariation'] = $catalog->variation;

            foreach($catalog->characteristics as $i => $characteristic) {
                $result['userInputs'][$i]['id'] = $characteristic->id;
                $result['userInputs'][$i]['name'] = $characteristic->name;
                $result['userInputs'][$i]['slug'] = $characteristic->slug;
                $result['userInputs'][$i]['type'] = $characteristic->type;

                class_exists($characteristic->type)
                    ? $result['userInputs'][$i]['modelId'] = $characteristic->pivot->value
                    : $result['userInputs'][$i]['value'] = $characteristic->pivot->value;
            }

            return $result;
        })->toArray();

        foreach($this->selectedCatalogs as $selectedCatalog) {
            $this->userInputs = $selectedCatalog['userInputs'];
            $this->loadVariationDependencies($selectedCatalog['selectedVariation']->id);
            $this->addCleanSlugToInputFormulaComponents();
            $this->calculate();

            $productName = $selectedCatalog['selectedVariation']->product->name;
            $variationName = $selectedCatalog['selectedVariation']->name;

            $this->calculatedProducts["$productName. $variationName"] = $this->calculated;
        }
    }
}
