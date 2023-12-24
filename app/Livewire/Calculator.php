<?php

namespace App\Livewire;

use App\Models\Material;
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
    public \Illuminate\Support\Collection $parameters;

    public int $selectedProductId;
    public Model $selectedProduct;

    public int $selectedVariationId;
    public Model $selectedVariation;

    public array $userInputs;
    public array $formulas;
    public array $calculated;
    public array $other;


    public function __construct()
    {
        $this->products = Product::all();
        $this->parameters = collect();
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
            ->with('components.parameters')
            ->firstWhere('id', $selectedVariationId);

        $this->components = $this->selectedVariation
            ->components;

        foreach ($this->components as $component) {
            foreach ($component->parameters as $parameter) {
                $this->parameters->push($parameter);
            }
        }

        $this->getInputs();
    }

    public function calculate(): void
    {
        foreach($this->userInputs as $i => $userInput){
            if($userInput['type'] == Material::class){
                $this->other[] = Material::query()->find($userInput['value']);
            }
        }

        foreach ($this->userInputs as $userInput) {
            foreach ($this->formulas as $parameterName => $parameterFormulas) {
                foreach($parameterFormulas as $i => $formula){
                    if ($userInput['slug'] === $formula['value']) {
                        $this->formulas[$parameterName][$i]['value'] = $userInput['value'];
                    }
                }
            }
        }

        $inlineFormulas = array_map(function($parameter){
            $formula = '';

            foreach($parameter as $formulaItem){
                $formula .= $formulaItem['value'];
            }

            return $formula;
        }, $this->formulas);

        $this->calculated = array_map(function($formula){
            return eval('return ' . $formula . ';');
        }, $inlineFormulas);
    }

    /**
     * Получает расчётные значения из формулы
     *
     * @return void
     */
    private function getInputs(): void
    {
        $formulas = $this->parameters->pluck('formula', 'name');
        $inputsToShow = [];

        $formulas = $formulas->map(
            fn(string $formula) => json_decode($formula, true)
        )
            ->toArray();

        foreach ($formulas as $parameter => $formula) {
            foreach ($formula as $i => $formulaValue) {
                $isParameterValue = preg_match('/^\[.*\]$/', $formulaValue['slug'], $foundedInputSlug);

                if (!$isParameterValue) {
                    $formulas[$parameter][$i]['value'] = $formulaValue['slug'];
                } else {
                    $trimmedName = trim($foundedInputSlug[0], '[]');

                    $formulas[$parameter][$i]['value'] = $trimmedName;
                    $inputsToShow[] = $trimmedName;
                }
            }
        }
        $this->formulas = $formulas;
        $this->printInputs($inputsToShow);
    }

    /**
     * Собирает на вывод список полей заполняемых пользователем
     *
     * @return void
     */
    private function printInputs(array $inputNames): void
    {
        $productsCharacteristics = $this->selectedProduct->characteristics;

        foreach ($inputNames as $input) {
            foreach ($productsCharacteristics as $characteristic) {
                if ($characteristic['slug'] === $input) $this->userInputs[] = $characteristic;
            }
        }
    }
}
