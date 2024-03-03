<?php

namespace App\Enums\Calculator;

use function Laravel\Prompts\select;

enum CalculateError : string
{
    /**
     * Ключ для хранения ошибок в сессии
     */
    public const SESSION_KEY = 'calculate_error';

    case DIVISION_BY_ZERO = 'В процессе вычисления произошло деление на 0';
    case PARSE_ERROR = 'Формула имеет не верный синтаксис';
    case ERROR_EXCEPTION = 'Ошибка при извлении значения';
    case UNDEFINED_EXCEPTION = 'Что-то пошло не так';

    public function set($context = null, $scope = null) : void
    {
        session()->put(self::SESSION_KEY, isset($context) ? "$context: $this->value" : $this->value);
    }
}
