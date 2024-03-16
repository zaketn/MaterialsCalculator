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
            fn(Collection $parameter) => $parameter->map(
                fn(string $formula) => json_decode($formula, true)
            )
        )
            ->toArray();

        foreach ($parameters as $parameterName => $formula) {
            foreach ($formula as $formulaName => $formulaItem) {
                foreach ($formulaItem as $i => $formulaValue) {
                    $formulaType = FormulaComponentType::matchType($formulaValue);
                    $parameters[$parameterName][$formulaName][$i]['type'] = $formulaType->name;

                    if ($formulaType === FormulaComponentType::SIMPLE) {
                        $parameters[$parameterName][$formulaName][$i]['value'] = $formulaValue['slug'];
                    } else {
                        $parameters[$parameterName][$formulaName][$i]['clean_slug'] = $formulaType->getCleanSlug($formulaValue['slug']);
                    }
                }
            }
        }

        return $parameters;
    }
}
