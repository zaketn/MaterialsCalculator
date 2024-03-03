<?php

namespace App\Livewire;

use App\Actions\Calculator\AddCleanSlugAction;
use App\Actions\Calculator\GetInputFormulaComponentsAction;
use App\Actions\Calculator\GetInputsFromCharacteristicsAction;
use App\Models\Product;
use App\Models\Variation;
use App\Services\Bitrix24\SendToBitrixService;
use App\Services\Calculator\CalculateService;
use App\Services\Calculator\CalculatorErrorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Component;
use ReflectionClass;

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
    private array $calculated;

    public ?string $bitrixDealId;
    public array $bitrixSendStatus;

    public function __construct()
    {
        $this->products = Product::all();
        $this->parameters = collect();

        if (isset($_REQUEST['PLACEMENT_OPTIONS'])) {
            $placementOptions = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
            $this->bitrixDealId = $placementOptions['ID'] ?? null;
        }
    }

    #[Computed]
    public function error() : ?string
    {
        return CalculatorErrorService::get();
    }

    #[Computed]
    public function calculated() : array
    {
        return $this->calculated ?? [];
    }

    public function updatedSelectedProductId(?int $selectedProductId): void
    {
        if (empty($selectedProductId)) {
            $this->unsetFields(['products']);

            return;
        }

        $this->selectedProduct = Product::query()
            ->find($selectedProductId);

        $this->variations = $this->selectedProduct
            ->variations;

        $this->userInputs = [];
    }

    public function updatedSelectedVariationId(?int $selectedVariationId): void
    {
        if (empty($selectedVariationId)) {
            $this->unsetFields(['products', 'selectedProduct', 'selectedProductId', 'variations']);

            return;
        }

        $this->loadVariationDependencies($selectedVariationId);
        $this->printInputs();
    }

    public function calculate(): void
    {
        $this->unsetFields([
            'products',
            'selectedProduct',
            'selectedProductId',
            'selectedVariation',
            'selectedVariationId',
            'variations',
            'userInputs',
            'formulas'
        ]);
        $this->calculated = [];

        foreach ($this->formulas as $formulaName => $formula) {
            if($formulaName === \App\Models\Component::SUMMARY_COMPONENT_NAME) continue;

            $calculateService = new CalculateService($this->userInputs, $formula);

            $this->calculated[$formulaName] = $calculateService->calculate();
        }

        $calculateService = new CalculateService(
            $this->userInputs,
            $this->formulas[\App\Models\Component::SUMMARY_COMPONENT_NAME]
        );

        $this->calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME] = $calculateService->calculateSummary($this->calculated);
    }

    /**
     * Кнопка "Очистить"
     *
     * Очищает все значения компонента, кроме тех, которые нужны для его начальной работы
     *
     * @return void
     */
    public function clearAll(): void
    {
        $this->unsetFields(['products']);
    }

    public function sendToBitrix() : void
    {
        $bitrixService = new SendToBitrixService(
            $this->bitrixDealId,
            $this->selectedProduct->name,
            $this->selectedVariation->name,
            $this->calculated,
        );

        unset($this->bitrixSendStatus);
        $errorStatus = [
            'class' => 'text-red-600',
            'text' => 'Ошибка при отправке в bitrix.'
        ];

        if(!$bitrixService->checkIfProductExists()){
            $isProductCreated = $bitrixService->createProduct();
            if(!$isProductCreated){
                $this->bitrixSendStatus = $errorStatus;
                return;
            }
        }

        $isProductAttached = $bitrixService->attachProductToDeal();
        if(!$isProductAttached){
            $this->bitrixSendStatus = $errorStatus;
            return;
        }

        $isCommentAttached = $bitrixService->createComment();

        if(!$isCommentAttached) {
            $this->bitrixSendStatus = $errorStatus;
            return;
        }

        $this->bitrixSendStatus = [
            'class' => 'text-green-500',
            'text' => 'Успех! Товар прикреплен к сделке, оставлен комментарий с подробностями.'
        ];
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

    private function unsetFields(array $exceptions) : void
    {
        CalculatorErrorService::remove();

        $reflect = new ReflectionClass(self::class);
        $props = $reflect->getProperties();

        foreach ($props as $prop) {
            if ($prop->class !== self::class || in_array($prop->getName(), $exceptions)) {
                continue;
            }

            $this->reset($prop->getName());
        }
    }
}
