<?php

declare(strict_types=1);

namespace App\Application\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class AlexTheme extends DefaultTheme
{
    public function applyTheme(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()->getFont()->setName($this->getFontName(Font::GEORGIA));
        $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(27);
        $sheet->freezePane('A2');
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12);

        $this->applyPlayerTheme($sheet);
        $this->applyToonProgressTheme($sheet);
        $this->applyPlayerEligibilityTheme($sheet);
        $this->applySummaryTheme($sheet);

        parent::applyTheme($spreadsheet);
    }

    private function applyPlayerTheme(Worksheet $sheet): void
    {
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())->applyFromArray($this->getHeaderStyles());
    }

    private function applyToonProgressTheme(Worksheet $sheet): void
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('B2:F' . $highestRow)->applyFromArray($this->getContentStyles());
        $sheet->getStyle('B2:B' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '9CC6FE'],
            ],
        ]);
        $sheet->getStyle('B1:F1')->applyFromArray($this->getHeaderStyles());

        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional->setOperatorType(Conditional::OPERATOR_EQUAL);
        $conditional->addCondition('"OK"');
        $conditional->getStyle()->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'C6E0B4'],
            ],
        ]);
        $conditional->setStopIfTrue(true);

        foreach ($sheet->getParent()->getNamedRange('toon_progress')->getCellsInRange() as $cell) {
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'B25651'],
                ],
            ]);
            $sheet->getStyle($cell)->setConditionalStyles([$conditional]);
        }
    }

    private function applyPlayerEligibilityTheme(Worksheet $sheet): void
    {
        $conditionals = [];
        $conditionals[] = new Conditional();
        $conditionals[0]->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionals[0]->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionals[0]->addCondition('2');
        $conditionals[0]->getStyle()->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '7C9149'],
            ],
        ]);
        $conditionals[0]->setStopIfTrue(true);

        $conditionals[] = new Conditional();
        $conditionals[1]->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionals[1]->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionals[1]->addCondition('1');
        $conditionals[1]->getStyle()->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'C6E0B4'],
            ],
        ]);
        $conditionals[1]->setStopIfTrue(true);

        $conditionals[] = new Conditional();
        $conditionals[2]->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionals[2]->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionals[2]->addCondition('0.5');
        $conditionals[2]->getStyle()->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F5C142'],
            ],
        ]);
        $conditionals[2]->setStopIfTrue(true);

        foreach ($sheet->getParent()->getNamedRange('player_eligibility')->getCellsInRange() as $cell) {
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'B25651'],
                ],
            ]);
            $sheet->getStyle($cell)->setConditionalStyles($conditionals);
        }
    }

    private function applySummaryTheme(Worksheet $sheet): void
    {
        $contentStyles = $this->getContentStyles();

        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getStyle('H1:M1')->applyFromArray($this->getHeaderStyles());
        $sheet->getStyle('H2:M2')->applyFromArray($contentStyles);
        $sheet->getStyle('L4:L' . $sheet->getHighestRow('L'))->applyFromArray($contentStyles);
        $sheet->getStyle('M4:M' . $sheet->getHighestRow('M'))->applyFromArray($contentStyles);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getContentStyles(): array
    {
        return [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getHeaderStyles(): array
    {
        return [
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
