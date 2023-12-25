<?php

namespace App\Actions\Calculator;

class GetInputsFromCharacteristicsAction
{
    /**
     * Сопоставляет компоненты формулы и характеристики продукта.
     *
     * Возвращается характеристика соответствующая компоненту формулы.
     *
     * @param array $inputFormulaComponents
     * @param array $productCharacteristics
     * @return array
     */
    public function __invoke(array $inputFormulaComponents, array $productCharacteristics) : array
    {
        $characteristicsToDisplay = [];

        foreach ($inputFormulaComponents as $input) {
            foreach ($productCharacteristics as $characteristic) {
                if ($characteristic['slug'] === $input){
                    $characteristicsToDisplay[] = $characteristic;
                }
            }
        }

        return $characteristicsToDisplay;
    }
}
