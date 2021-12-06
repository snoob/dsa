<?php

declare(strict_types=1);

namespace App\Application\Spreadsheet;

final class FormulaUtil
{
    /**
     * @param array<int, string> $cells
     */
    public static function createSumFormula(array $cells): string
    {
        return '=' . implode('+', $cells);
    }
}
