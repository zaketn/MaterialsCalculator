<?php

namespace App\Enums\Calculator;

enum FormulaComponentType
{
    case SIMPLE;
    case INPUT;
    case CALCULATED;
    case FROM_PARENT;
}
