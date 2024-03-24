<?php

namespace App\Livewire;

use App\Actions\Calculator\AddCleanSlugAction;
use App\Models\Variation;
use App\Services\Calculator\CalculateService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

abstract class CalculatableComponent extends Component
{
    public Collection $components;
    public \Illuminate\Support\Collection $parameters;
    public Model $selectedVariation;
    public array $userInputs;
    public array $calculated;
    public array $formulas;

    public function calculate(): void
    {
        $this->calculated = [];
        $this->userInputs = $this->fillPrices($this->userInputs);

        foreach ($this->formulas as $formulaName => $formula) {
            if ($formulaName === \App\Models\Component::SUMMARY_COMPONENT_NAME) continue;

            $calculateService = new CalculateService($this->userInputs, $formula);

            $this->calculated[$formulaName] = $calculateService->calculate();
        }

        $calculateService = new CalculateService(
            $this->userInputs,
            $this->formulas[\App\Models\Component::SUMMARY_COMPONENT_NAME]
        );

        $this->calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME] = $calculateService->calculateSummary($this->calculated);
    }

    protected function loadVariationDependencies(int $selectedVariationId): void
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

    protected function addCleanSlugToInputFormulaComponents(): void
    {
        $addCleanSlugAction = new AddCleanSlugAction();

        $separatedFormulaComponents = $addCleanSlugAction($this->parameters);

        $this->formulas = $separatedFormulaComponents;
    }

    /**
     * Заполняет цены у class-based характеристик
     *
     * @param array $userInputs
     * @return array
     */
    protected function fillPrices(array $userInputs) : array
    {
        foreach($userInputs as &$userInput) {
            if(!class_exists($userInput['type'])) continue;

            $model = $userInput['type']::query()->firstWhere('id', $userInput['modelId']);
            $userInput['value'] = $model->price;
        }

        return $userInputs;
    }
}
