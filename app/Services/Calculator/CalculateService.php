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

    /**
     * Вычисление параметров
     *
     * @return array
     */
    public function calculate(): array
    {
        return $this->exec(function () {
            $this->fillFormulaValues();

            return $this->calculateParameters();
        }, []);
    }

    /**
     * Вычисление поля "Итог", которое может содержать поля, из любого компонента
     *
     * @param array $calculated
     * @return float
     */
    public function calculateSummary(array $calculated): float
    {
        return $this->exec(function () use ($calculated) {
            $this->fillFormulaValues();
            $this->fillFromParentValues($calculated);

            return $this->calculateParameter($this->formulas[Parameter::SUMMARY_PARAMETER_NAME]);
        }, 0);
    }

    /**
     * Вспомогательная функция для отлова ошибок вычисления
     *
     * @param Closure $calculation
     * @param mixed|null $default
     * @return mixed
     */
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

    /**
     * Берёт значения для расчёта поля "Итог", из уже расчитанных компонентов
     *
     * @param array $calculated
     * @return void
     */
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

    /**
     * Заполняет параметры, вводимые пользователем
     *
     * @return void
     */
    private function fillFormulaValues(): void
    {
        $this->context = 'Заполнение значений';

        foreach ($this->userInputs as $userInput) {
            foreach ($this->formulas as $parameterName => $parameterFormulas) {
                foreach ($parameterFormulas as $i => $formula) {
                    if (!isset($formula['clean_slug']) || $userInput['slug'] !== $formula['clean_slug']) continue;

                    $this->formulas[$parameterName][$i]['value'] = $userInput['value'];
                }
            }
        }
    }

    /**
     * Расчёт параметров компонента
     *
     * @return array
     */
    private function calculateParameters(): array
    {
        $this->context = 'Вычисление отдельно взятого компонента';

        if ($this->isCalculatingFinished()) return $this->formulas;

        $this->calculateSimpleParameters();
        $this->calculateComplexParameters();

        $this->calculateParameters();

        return $this->formulas;
    }

    /**
     * Непосредственно математическое вычисление формул
     *
     * @param array $parameter
     * @return mixed
     */
    private function calculateParameter(array $parameter): mixed
    {
        $this->context = 'Составление формулы компонента/общей суммы';
        $formula = '';

        Log::debug($parameter);
        foreach ($parameter as $formulaItem) {
            Log::debug($formulaItem);

            $formula .= $formulaItem['value'];
        }

        $this->context = 'Вычисление отдельно взятого компонента. Попробуйте проверить корректность формул.';
        $result = eval("return $formula;");

        return is_float($result) ? round($result, 2) : $result;
    }

    /**
     * Преобразование параметров, которые нельзя математически вычислить в данный момент
     *
     * @return void
     */
    private function calculateComplexParameters(): void
    {
        foreach ($this->formulas as $parameterName => $parameter) {
            if ($this->isParameterSimple($parameter)) continue;

            foreach ($parameter as $i => $parameterComponent) {
                if (!$this->shouldProcess($parameterName, $parameterComponent)) continue;

                $this->formulas[$parameterName][$i]['value'] = $this->formulas[$parameterComponent['inner']];
            }
        }
    }

    /**
     * Вычисление параметров которые готовы к математическому вычислению
     *
     * @return void
     */
    private function calculateSimpleParameters(): void
    {
        foreach ($this->formulas as $parameterName => $parameter) {
            if (!is_array($parameter) || !$this->isParameterSimple($parameter)) continue;

            $this->formulas[$parameterName] = $this->calculateParameter($parameter);
        }
    }

    /**
     * Проверка параметра на возможность математического вычисления
     *
     * @param array|int|float $parameter
     * @return bool
     */
    private function isParameterSimple(array|int|float $parameter): bool
    {
        if (!is_array($parameter)) return true;

        foreach ($parameter as $parameterComponent) {
            if ($parameterComponent['type'] === FormulaComponentType::CALCULATED->name && !isset($parameterComponent['value']))
                return false;
        }

        return true;
    }

    /**
     * Проверка вычисляемого параметра на необходимость дальнейшей обработки в простой параметр
     *
     * @param string $parameterName
     * @param array $parameterComponent
     * @return bool
     */
    private function shouldProcess(string $parameterName, array $parameterComponent): bool
    {
        if ($parameterComponent['type'] !== FormulaComponentType::CALCULATED->name || isset($parameterComponent['value'])) {
            return false;
        }

        if (!is_array($this->formulas[$parameterName]) || !$this->isParameterCalculated($parameterComponent['inner'])) {
            return false;
        }

        return true;
    }

    /**
     * Проверка на то, вычислен ли уже параметр
     *
     * @param string $parameterName
     * @return bool
     */
    private function isParameterCalculated(string $parameterName): bool
    {
        foreach ($this->formulas as $name => $parameter) {
            if ($name === $parameterName && is_numeric($parameter)) return true;
        }

        return false;
    }

    /**
     * Проверка на то, что все расчёты окончены
     *
     * @return bool
     */
    private function isCalculatingFinished(): bool
    {
        foreach ($this->formulas as $parameter) {
            if (is_array($parameter)) return false;
        }

        return true;
    }
}
