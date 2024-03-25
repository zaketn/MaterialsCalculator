<?php

namespace App\Livewire;

use App\Actions\Calculator\GetInputFormulaComponentsAction;
use App\Actions\Calculator\GetInputsFromCharacteristicsAction;
use App\Models\Product;
use App\Services\Bitrix24\SendToBitrixService;
use App\Services\Calculator\CalculatorErrorService;
use App\Services\Catalog\CatalogService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use ReflectionClass;

class Calculator extends CalculatableComponent
{
    public Collection $products;
    public Collection $variations;

    public int $selectedProductId;
    public Model $selectedProduct;

    public int $selectedVariationId;

    public ?string $bitrixDealId;
    public array $bitrixSendStatus;
    public string $nameForCatalog;

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
    public function error(): ?string
    {
        return CalculatorErrorService::get();
    }

    #[Computed]
    public function calculated(): array
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

    public function calculate(): void {
        $this->unsetFields([
            'products',
            'selectedProduct',
            'selectedProductId',
            'selectedVariation',
            'selectedVariationId',
            'variations',
            'userInputs',
            'formulas',
            'bitrixDealId'
        ]);

        parent::calculate();
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

    public function saveCalculations(): void
    {
        $this->bitrixSendStatus = [];

        if(empty($this->nameForCatalog)) {
            $this->bitrixSendStatus[] = [
                'class' => 'text-red-600',
                'text' => 'Вы должны заполнить поле с названием.'
            ];

            return;
        }

        $isRecordAdded = CatalogService::addRecord(
            $this->nameForCatalog,
            $this->selectedProduct->id,
            $this->selectedVariation->id,
            $this->userInputs
        );

        if($isRecordAdded === true) {
            $this->bitrixSendStatus[] = [
                'class' => 'text-green-500',
                'text' => 'Товар сохранён в каталог.'
            ];
        } else {
            $this->bitrixSendStatus[] = [
                'class' => 'text-red-600',
                'text' => 'Ошибка при сохранении товара.'
            ];
        }

        if (app()->isProduction()) {
            $this->sendToBitrix();
        }
    }


    private function sendToBitrix(): void
    {
        $bitrixService = new SendToBitrixService(
            $this->bitrixDealId,
            $this->selectedProduct->name,
            $this->selectedVariation->name,
            $this->calculated,
        );

        $errorStatus[] = [
            'class' => 'text-red-600',
            'text' => 'Ошибка при отправке в bitrix.'
        ];

        if (!$bitrixService->checkIfProductExists()) {
            $isProductCreated = $bitrixService->createProduct();
            if (!$isProductCreated) {
                $this->bitrixSendStatus = $errorStatus;
                return;
            }
        }

        $isProductAttached = $bitrixService->attachProductToDeal();
        if (!$isProductAttached) {
            $this->bitrixSendStatus = $errorStatus;
            return;
        }

        $this->bitrixSendStatus[] = [
            'class' => 'text-green-500',
            'text' => 'Товар прикреплён к сделке.'
        ];

        $isCommentAttached = $bitrixService->createComment();

        if (!$isCommentAttached) {
            $this->bitrixSendStatus = $errorStatus;
            return;
        }

        $this->bitrixSendStatus[] = [
            'class' => 'text-green-500',
            'text' => 'Оставлен комментарий с подробностями.'
        ];
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

    private function unsetFields(array $exceptions): void
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
