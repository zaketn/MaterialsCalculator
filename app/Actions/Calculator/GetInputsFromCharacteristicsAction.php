<?php

namespace App\Actions\Calculator;

use Illuminate\Database\Eloquent\Collection;

class GetInputsFromCharacteristicsAction
{
    /**
     * Сопоставляет компоненты формулы и характеристики продукта.
     *
     * Возвращается характеристика соответствующая компоненту формулы.
     *
     * @param array $inputFormulaComponents
     * @param Collection $productCharacteristics
     * @return array
     */
    public function __invoke(array $inputFormulaComponents, Collection $productCharacteristics) : array
    {
        $characteristicsToDisplay = [];

        foreach ($inputFormulaComponents as $input) {
            foreach ($productCharacteristics as $characteristic) {
                if ($characteristic->slug === $input){
                    $characteristicsToDisplay[] = $characteristic;
                }
            }
        }

        return collect($characteristicsToDisplay)
            ->unique()
            ->toArray();
    }
}
