<?php

declare(strict_types=1);

namespace App\Application\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DefaultTheme
{
    public function applyTheme(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $this->applyHeaderTheme($sheet);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }

    protected function getHeaderRange(Worksheet $sheet): string
    {
        return sprintf('A1:%s1', $sheet->getHighestColumn());
    }

    private function applyHeaderTheme(Worksheet $sheet): void
    {
        $sheet->getRowDimension(1)->setRowHeight(27);
        $sheet->getStyle($this->getHeaderRange($sheet))->applyFromArray($this->getHeaderStyles());
        $sheet->freezePane('A2');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getHeaderStyles(): array
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '6DAAFE'],
            ],
            'font' => [
                'bold' => true,
            ],
        ];
    }
}
