<?php

namespace App\Actions\Calculator;

class GetInputFormulaComponentsAction
{
    /**
     * Получает вводимые компоненты формулы.
     *
     * @param array $parameters
     * @return array
     */
    public function __invoke(array $parameters) : array
    {
        $inputFormulaComponents = [];

        foreach ($parameters as $parameter) {
            foreach($parameter as $formulaItem) {
                foreach($formulaItem as $formulaValue) {
                    if(isset($formulaValue['clean_slug'])){
                        $inputFormulaComponents[] = $formulaValue['clean_slug'];
                    }
                }
            }
        }

        return $inputFormulaComponents;
    }
}
