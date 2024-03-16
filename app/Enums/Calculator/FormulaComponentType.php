<?php

namespace App\Enums\Calculator;

enum FormulaComponentType
{
    case SIMPLE;
    case INPUT;
    case CALCULATED;
    case FROM_PARENT;

    public static function matchType(array $formulaValue): self
    {
        if (isset($formulaValue['parent'])) {
            return self::FROM_PARENT;
        }

        if (preg_match('/^\[.*]$/', $formulaValue['slug'])) {
            return self::INPUT;
        }

        if (preg_match('/^\{.*}$/', $formulaValue['slug'])) {
            return self::CALCULATED;
        }

        return self::SIMPLE;
    }

    public function getCleanSlug(string $slug) : string
    {
        return match($this) {
          FormulaComponentType::FROM_PARENT, FormulaComponentType::INPUT => trim($slug, '[]'),
          FormulaComponentType::CALCULATED => trim($slug, '{}'),
          FormulaComponentType::SIMPLE => $slug
        };
    }
}
