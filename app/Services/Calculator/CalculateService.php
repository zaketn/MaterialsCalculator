<?php

namespace App\Services\Calculator;

use App\Enums\Calculator\CalculateError;
use App\Enums\Calculator\FormulaComponentType;
use App\Models\Parameter;
use Closure;
use Illuminate\Support\Facades\Log;

class CalculateService
{
    private string $context = '';

    public function __construct(
        private readonly array $userInputs,
        private array          $formulas,
    )
    {
    }

    public function calculate(): array
    {
        return $this->exec(function () {
            $this->fillFormulaValues();

            return $this->calculateParameters();
        }, []);
    }

    public function calculateSummary(array $calculated): float
    {
        return $this->exec(function () use ($calculated) {
            $this->fillFormulaValues();
            $this->fillFromParentValues($calculated);

            return $this->calculateParameter($this->formulas[Parameter::SUMMARY_PARAMETER_NAME]);
        }, 0);
    }

    private function exec(Closure $calculation, mixed $default = null): mixed
    {
        if ($default !== null) {
            $result = $default;
        }

        try {
            $result = $calculation();
        } catch (\ErrorException) {
            CalculateError::ERROR_EXCEPTION->set($this->context);
        } catch (\DivisionByZeroError) {
            CalculateError::DIVISION_BY_ZERO->set($this->context);
        } catch (\ParseError) {
            CalculateError::PARSE_ERROR->set($this->context);
        } catch (\Exception $e) {
            Log::debug($e);
            CalculateError::UNDEFINED_EXCEPTION->set($this->context);
        }

        return $result;
    }

    private function fillFromParentValues(array $calculated): void
    {
        $this->context = 'Заполнение итоговых значений';

        foreach ($this->formulas[Parameter::SUMMARY_PARAMETER_NAME] as $i => $formula) {
            Log::debug('inner');
            Log::debug($formula);

            foreach ($calculated as $component => $calculatedParameter) {
                foreach ($calculatedParameter as $parameterName => $parameterValue) {
                    Log::debug('Component');
                    Log::debug($component);

                    Log::debug('Slug parameter name');
                    Log::debug($parameterName);

                    if ($formula['type'] === FormulaComponentType::FROM_PARENT->name && $formula['parent'] === $component && $formula['inner'] === $parameterName) {
                        $this->formulas[Parameter::SUMMARY_PARAMETER_NAME][$i]['value'] = $parameterValue;
                        Log::debug('Calculated!');
                        Log::debug($this->formulas[Parameter::SUMMARY_PARAMETER_NAME][$i]);
                    }
                }
            }
        }
    }

    private function fillFormulaValues(): void
    {
        $this->context = 'Заполнение значений';

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

    private function calculateParameters(): array
    {
        $this->context = 'Вычисление отдельно взятого параметра';

        if ($this->isCalculatingFinished()) return $this->formulas;

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
        if (!is_array($parameter)) return true;

        foreach ($parameter as $parameterComponent) {
            if ($parameterComponent['type'] === FormulaComponentType::CALCULATED->name && !isset($parameterComponent['value']))
                return false;
        }

        return true;
    }

    private function calculateParameter(array $parameter): mixed
    {
        $this->context = 'Составление итоговой формулы параметра/общей суммы';
        $formula = '';

        Log::debug(collect($parameter));
        foreach ($parameter as $formulaItem) {
            Log::debug($formulaItem);

            $formula .= $formulaItem['value'];
        }

        $this->context = 'Вычисление итогового примера. Попробуйте проверить корректность формул.';
        $result = eval("return $formula;");

        return is_float($result) ? round($result, 2) : $result;
    }

    private function isParameterCalculated(string $parameterName): bool
    {
        foreach ($this->formulas as $name => $parameter) {
            if ($name === $parameterName && is_numeric($parameter)) return true;
        }

        return false;
    }

    private function isCalculatingFinished(): bool
    {
        foreach ($this->formulas as $parameter) {
            if (is_array($parameter)) return false;
        }

        return true;
    }
}
