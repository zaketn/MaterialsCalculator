<?php

namespace App\Actions\Calculator;

class FillFormulaValuesAction
{
    public function __invoke(array $userInputs, array $formulas) : array
    {
        foreach ($userInputs as $userInput) {
            foreach ($formulas as $parameterName => $parameterFormulas) {
                foreach($parameterFormulas as $i => $formula){
                    if (isset($formula['clean_slug']) && $userInput['slug'] === $formula['clean_slug']) {
                        $formulas[$parameterName][$i]['value'] = $userInput['value'];
                    }
                }
            }
        }

        return $formulas;
    }
}
