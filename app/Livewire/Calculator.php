<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Calculator extends Component
{
    public Collection $products;
    public Collection $variations;
    public Collection $components;
    public Collection $parameters;

    public int $selectedProductId;
    public Model $selectedProduct;

    public int $selectedVariationId;
    public Model $selectedVariation;



    public function __construct()
    {
        $this->products = Product::all();
    }

    public function updatedSelectedProductId(int $selectedProductId): void
    {
        $this->selectedProduct = Product::query()
            ->find($selectedProductId);

        $this->variations = $this->selectedProduct
            ->variations;
    }

    public function updatedSelectedVariationId(int $selectedVariationId): void
    {
        $this->selectedVariation = Variation::query()
            ->find($selectedVariationId);

        $this->components = $this->selectedVariation
            ->components;
    }
}
