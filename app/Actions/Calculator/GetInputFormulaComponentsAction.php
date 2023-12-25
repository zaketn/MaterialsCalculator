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
            foreach($parameter as $formulaComponent) {
                if(isset($formulaComponent['clean_slug'])){
                    $inputFormulaComponents[] = $formulaComponent['clean_slug'];
                }
            }
        }

        return $inputFormulaComponents;
    }
}
