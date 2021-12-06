<?php

declare(strict_types=1);

namespace App\Application\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DefaultTheme
{
    public function applyTheme(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
    }

    protected function getFontName(string $fontFile): string
    {
        return ucfirst(substr($fontFile, 0, -4));
    }
}
