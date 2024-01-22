<?php

namespace App\Services\Bitrix24;

use App\Models\Component;
use Illuminate\Support\Facades\Log;

class SendToBitrixService
{
    private string $productTitle;
    private ?int $summaryPrice;
    private int $bitrixProductId;

    public function __construct(
        private int    $bitrixDealId,
        private string $productName,
        private string $variationName,
        private array  $parameters,
    )
    {
        $this->productTitle = $this->init();
    }

    /**
     * Создает название и берёт значение поля с итоговой ценой.
     *
     * @return string
     */
    private function init(): string
    {
        $parameterNameValue = '';
        foreach ($this->parameters as $name => $components) {
            if ($name === Component::SUMMARY_COMPONENT_NAME) continue;

            foreach ($components as $parameterKey => $parameterValue) {
                $parameterNameValue .= mb_strtolower($parameterKey) . " - $parameterValue, ";
            }
        }
        $parameterNameValue = trim($parameterNameValue, ", ");

        $this->summaryPrice = (int)$this->parameters[Component::SUMMARY_COMPONENT_NAME];

        return "$this->productName $this->variationName($parameterNameValue)";
    }

    public function checkIfProductExists(): bool
    {
        $products = CRest::call('crm.product.list', [
            'select' => ['ID', 'NAME', 'CURRENCY_ID', 'PRICE']
        ]);

        if (empty($products['result'])) {
            Log::debug($products);
            abort(500, 'Проблема с обращением к bitrix24 API');
        }

        foreach ($products['result'] as $product) {
            if ($product['NAME'] === $this->productTitle) {
                $this->bitrixProductId = $product['ID'];

                return true;
            }
        }

        return false;
    }

    public function createProduct(): bool
    {
        $productData = [
            'NAME' => $this->productTitle,
        ];

        if (isset($this->summaryPrice)) {
            $productData['CURRENCY_ID'] = 'RUB';
            $productData['PRICE'] = $this->summaryPrice;
        }

        $response = CRest::call('crm.product.add', [
            'fields' => $productData
        ]);

        if (empty($response['result'])) {
            Log::debug($response);

            return false;
        }

        $this->bitrixProductId = $response['result'];

        return true;
    }

    public function attachProductToDeal(): bool
    {
        $currentProducts = CRest::call('crm.deal.productrows.get', [
            'id' => $this->bitrixDealId,
        ]);

        if (!isset($currentProducts['result'])) {
            Log::debug($currentProducts);

            return false;
        }
        $currentProducts = $currentProducts['result'];

        $currentProducts[] = [
            'PRODUCT_ID' => $this->bitrixProductId,
            'PRICE' => $this->summaryPrice,
            'QUANTITY' => 1
        ];

        $response = CRest::call('crm.deal.productrows.set', [
            'id' => $this->bitrixDealId,
            'rows' => $currentProducts
        ]);

        if (!isset($response['result']) && $response['result'] !== true) {
            Log::debug($response);

            return false;
        }

        return true;
    }

    public function createComment(): bool
    {
        $parameterNameValue = "Продукт: $this->productName\nВариация: $this->variationName\n";
        foreach ($this->parameters as $components) {
            foreach ($components as $parameterKey => $parameterValue) {
                $parameterNameValue .= "$parameterKey - $parameterValue\n";
            }
        }

        $response = CRest::call('crm.timeline.comment.add', [
            'fields' => [
                'ENTITY_ID' => $this->bitrixDealId,
                'ENTITY_TYPE' => 'deal',
                'COMMENT' => $parameterNameValue,
            ]
        ]);

        if (!isset($response['result'])) {
            Log::debug($response);

            return false;
        }

        return true;
    }
}
