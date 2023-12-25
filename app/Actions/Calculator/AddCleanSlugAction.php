<?php

namespace App\Actions\Calculator;

use Illuminate\Support\Collection;

class AddCleanSlugAction
{
    /**
     * Добавляет поле clean_slug к вводимым компонентам формулы.
     *
     * Принцип: компонент формулы, у которого slug в формате [component_slug] - вводится пользователем и создается
     * поле clean_slug, убирающее квадратные скобки из [component_slug].
     *
     * @param Collection $formulas
     * @return array
     */
    public function __invoke(Collection $formulas) : array
    {
        $formulas = $formulas->map(
            fn(string $formula) => json_decode($formula, true)
        )
            ->toArray();

        foreach ($formulas as $parameter => $formula) {
            foreach ($formula as $i => $formulaValue) {
                $isParameterValue = preg_match('/^\[.*]$/', $formulaValue['slug'], $foundedInputSlug);

                if (!$isParameterValue) {
                    $formulas[$parameter][$i]['value'] = $formulaValue['slug'];
                } else {
                    $trimmedName = trim($foundedInputSlug[0], '[]');
                    $formulas[$parameter][$i]['clean_slug'] = $trimmedName;
                }
            }
        }


        return $formulas;
    }
}
