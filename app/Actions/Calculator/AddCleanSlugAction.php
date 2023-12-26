<?php

namespace App\Actions\Calculator;

use App\Enums\Calculator\FormulaComponentType;
use Illuminate\Support\Collection;

class AddCleanSlugAction
{
    /**
     * Добавляет поле clean_slug к вводимым компонентам формулы, или к компонентам, которые рассчитаны на основе
     * введённых компонентов.
     *
     * @param Collection $parameters
     * @return array
     */
    public function __invoke(Collection $parameters): array
    {
        $parameters = $parameters->map(
            fn(Collection $parameter) => $parameter->map(fn(string $formula) => json_decode($formula, true))
        )
            ->toArray();

//        dd($parameters);

        foreach ($parameters as $parameterName => $formula) {
            foreach ($formula as $formulaName => $formulaItem) {
                foreach($formulaItem as $i => $formulaValue) {
                    $isParameterValue = preg_match('/^\[.*]$/', $formulaValue['slug'], $foundedInputSlug);
                    $isCalculatedValue = preg_match('/^\{.*}$/', $formulaValue['slug'], $foundedCalculatedSlug);

                    if (!$isParameterValue && !$isCalculatedValue) {
                        $parameters[$parameterName][$formulaName][$i]['value'] = $formulaValue['slug'];
                        $parameters[$parameterName][$formulaName][$i]['type'] = FormulaComponentType::SIMPLE->name;
                    } else {
                        $trimmedName = trim($foundedInputSlug[0] ?? $foundedCalculatedSlug[0], '[]{}');
                        $parameters[$parameterName][$formulaName][$i]['clean_slug'] = $trimmedName;

                        if (isset($foundedInputSlug[0])) {
                            $parameters[$parameterName][$formulaName][$i]['type'] = FormulaComponentType::INPUT->name;
                        } else if (isset($foundedCalculatedSlug[0])) {
                            $parameters[$parameterName][$formulaName][$i]['type'] = FormulaComponentType::CALCULATED->name;
                        }
                    }
                }
            }
        }

        return $parameters;
    }
}
