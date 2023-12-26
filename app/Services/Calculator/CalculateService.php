<?php

namespace App\Services\Calculator;

use App\Enums\Calculator\FormulaComponentType;

class CalculateService
{
    public function __construct(
        private readonly array $userInputs,
        private array          $formulas,
    )
    {
    }

    public function calculate(): array
    {
        $this->fillFormulaValues();

        return $this->calculateParameters();
    }

    private function fillFormulaValues(): void
    {
        foreach ($this->userInputs as $userInput) {
            foreach ($this->formulas as $parameterName => $parameterFormulas) {
                foreach ($parameterFormulas as $i => $formula) {
                    if (isset($formula['clean_slug']) && $userInput['slug'] === $formula['clean_slug']) {
                        $this->formulas[$parameterName][$i]['value'] = $userInput['value'];
                    }
                }
            }
        }
    }

    private function calculateParameters() : array
    {
        if($this->isCalculatingFinished()) return $this->formulas;

        foreach ($this->formulas as $parameterName => $parameter) {
            if (is_array($parameter) && $this->isParameterSimple($parameter)) {
                $this->formulas[$parameterName] = $this->calculateParameter($parameter);
            } else if (!$this->isParameterSimple($parameter)) {
                foreach ($parameter as $i => $parameterComponent) {
                    if ($parameterComponent['type'] === FormulaComponentType::CALCULATED->name && !isset($parameterComponent['value'])) {
                        if (is_array($this->formulas[$parameterName]) && $this->isParameterCalculated($parameterComponent['inner'])) {
                            $this->formulas[$parameterName][$i]['value'] = $this->formulas[$parameterComponent['inner']];
                        }
                    }
                }
                $this->calculateParameters();
            }
        }

        return $this->formulas;
    }

    private function isParameterSimple(array|int|float $parameter): bool
    {
        if(!is_array($parameter)) return true;

        foreach ($parameter as $parameterComponent) {
            if ($parameterComponent['type'] === FormulaComponentType::CALCULATED->name && !isset($parameterComponent['value']))
                return false;
        }

        return true;
    }

    private function calculateParameter(array $parameter)
    {
        $formula = '';

        foreach ($parameter as $formulaItem) {
            $formula .= $formulaItem['value'];
        }

        return eval("return $formula;");
    }

    private function isParameterCalculated(string $parameterName)
    {
        foreach ($this->formulas as $name => $parameter) {
            if ($name === $parameterName && is_numeric($parameter)) return true;
        }

        return false;
    }

    private function isCalculatingFinished() : bool
    {
        foreach ($this->formulas as $parameter) {
            if(is_array($parameter)) return false;
        }

        return true;
    }
}
