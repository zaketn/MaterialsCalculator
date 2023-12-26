<?php

namespace App\Livewire;

use App\Actions\Calculator\AddCleanSlugAction;
use App\Actions\Calculator\GetInputFormulaComponentsAction;
use App\Actions\Calculator\GetInputsFromCharacteristicsAction;
use App\Models\Product;
use App\Models\Variation;
use App\Services\Calculator\CalculateService;
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

        $this->userInputs = [];
    }

    public function updatedSelectedVariationId(int $selectedVariationId): void
    {
        $this->loadVariationDependencies($selectedVariationId);
        $this->printInputs();
    }

    public function calculate(): void
    {
        foreach($this->formulas as $formulaName => $formula) {
            $calculateService = new CalculateService($this->userInputs, $formula);

            $this->calculated[$formulaName] = $calculateService->calculate();
        }
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
            $this->parameters->put(
                $component->name,
                $component->parameters->pluck('formula', 'name')
            );
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

        $separatedFormulaComponents = $addCleanSlugAction($this->parameters);

        $this->formulas = $separatedFormulaComponents;
    }
}
