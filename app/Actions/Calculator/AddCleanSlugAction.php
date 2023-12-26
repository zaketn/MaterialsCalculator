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
     * @param Collection $formulas
     * @return array
     */
    public function __invoke(Collection $formulas): array
    {
        $formulas = $formulas->map(
            fn(string $formula) => json_decode($formula, true)
        )
            ->toArray();

        foreach ($formulas as $parameter => $formula) {
            foreach ($formula as $i => $formulaValue) {
                $isParameterValue = preg_match('/^\[.*]$/', $formulaValue['slug'], $foundedInputSlug);
                $isCalculatedValue = preg_match('/^\{.*}$/', $formulaValue['slug'], $foundedCalculatedSlug);

                if (!$isParameterValue && !$isCalculatedValue) {
                    $formulas[$parameter][$i]['value'] = $formulaValue['slug'];
                    $formulas[$parameter][$i]['type'] = FormulaComponentType::SIMPLE->name;
                } else {
                    $trimmedName = trim($foundedInputSlug[0] ?? $foundedCalculatedSlug[0], '[]{}');
                    $formulas[$parameter][$i]['clean_slug'] = $trimmedName;

                    if (isset($foundedInputSlug[0])) {
                        $formulas[$parameter][$i]['type'] = FormulaComponentType::INPUT->name;
                    } else if (isset($foundedCalculatedSlug[0])) {
                        $formulas[$parameter][$i]['type'] = FormulaComponentType::CALCULATED->name;
                    }
                }
            }
        }


        return $formulas;
    }
}
