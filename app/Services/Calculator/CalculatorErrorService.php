<?php

namespace App\Services\Calculator;

use App\Enums\Calculator\CalculateError;

class CalculatorErrorService
{
    public static function get() : ?string
    {
        try{
            $error = session()->pull(CalculateError::SESSION_KEY);
        } catch(\Exception) {
            $error = '';
        }

        return $error;
    }

    public static function remove() : void
    {
        session()->remove(CalculateError::SESSION_KEY);
    }
}
