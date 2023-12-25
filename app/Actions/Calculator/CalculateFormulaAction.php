<?php

namespace App\Actions\Calculator;

class CalculateFormulaAction
{
    public function __invoke(array $formulas) : array
    {
        $inlineFormulas = array_map(function ($parameter) {
            $formula = '';

            foreach ($parameter as $formulaItem) {
                $formula .= $formulaItem['value'];
            }

            return $formula;
        }, $formulas);

        return array_map(
            fn($formula) => eval('return ' . $formula . ';'),
            $inlineFormulas
        );
    }
}
