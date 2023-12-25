<?php

namespace App\Livewire;

use App\Actions\Calculator\AddCleanSlugAction;
use App\Actions\Calculator\CalculateFormulaAction;
use App\Actions\Calculator\FillFormulaValuesAction;
use App\Actions\Calculator\GetInputFormulaComponentsAction;
use App\Actions\Calculator\GetInputsFromCharacteristicsAction;
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
        $this->loadVariationDependencies($selectedVariationId);
        $this->printInputs();
    }

    public function calculate(): void
    {
        $fillFormulaValuesAction = new FillFormulaValuesAction();
        $this->formulas = $fillFormulaValuesAction($this->userInputs, $this->formulas);

        $calculateFormulaAction = new CalculateFormulaAction();
        $this->calculated = $calculateFormulaAction($this->formulas);
    }

    private function loadVariationDependencies(int $selectedVariationId): void
    {
        $this->selectedVariation = Variation::query()
            ->with('components.parameters')
            ->firstWhere('id', $selectedVariationId);

        $this->components = $this->selectedVariation
            ->components;

        $this->parameters = collect();
        foreach ($this->components as $component) {
            foreach ($component->parameters as $parameter) {
                $this->parameters->push($parameter);
            }
        }
    }

    private function printInputs(): void
    {
        $this->addCleanSlugToInputFormulaComponents();

        $getInputFormulaComponentsAction = new GetInputFormulaComponentsAction();
        $getInputsFromCharacteristicsAction = new GetInputsFromCharacteristicsAction();

        $inputFormulaComponents = $getInputFormulaComponentsAction($this->formulas);

        $characteristicsToDisplay = $getInputsFromCharacteristicsAction(
            $inputFormulaComponents,
            $this->selectedProduct->characteristics
        );

        $this->userInputs = $characteristicsToDisplay;
    }

    private function addCleanSlugToInputFormulaComponents(): void
    {
        $addCleanSlugAction = new AddCleanSlugAction();

        $separatedFormulaComponents = $addCleanSlugAction(
            $this->parameters->pluck('formula', 'name')
        );

        $this->formulas = $separatedFormulaComponents;
    }
}
