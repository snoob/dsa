<?php

declare(strict_types=1);

namespace App\Application\Command\Club;

use App\Application\Command\CommandOutputStyle;
use App\Application\Spreadsheet\DefaultTheme;
use App\Domain\Club\Club;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'club:export:team-builder',
    description: 'Export team builder club data.',
)]
final class ExportClubTeamBuilderCommand extends AbstractExportClubCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandOutputStyle = new CommandOutputStyle($input, $output);
        $stopwatch = new Stopwatch(true);
        $stopwatch->start($this->getName());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->appendHeaderRows($sheet);
        $this->appendContent($sheet, $this->getClub($commandOutputStyle));

        $theme = new DefaultTheme();
        $theme->applyTheme($spreadsheet);

        $xlsx = new Xlsx($spreadsheet);
        $filePath = sprintf('%s/%s.xlsx', $this->exportDir, 'team-builder');
        $xlsx->save($filePath);
        $output->writeln(sprintf('%s written in %d sec', $filePath, $stopwatch->getEvent($this->getName())->getDuration() / 1000));
        $output->writeln(sprintf('Memory usage %d MB', $stopwatch->getEvent($this->getName())->getMemory() / 1024 / 1024));

        return Command::SUCCESS;
    }

    private function appendHeaderRows(Worksheet $sheet): void
    {
        $sheet->fromArray(['', '']);
    }

    private function appendContent(Worksheet $sheet, Club $club): void
    {
        foreach ($club->getPlayers() as $player) {
            $currentRow = $sheet->getHighestRow() + 1;
            $sheet->fromArray([$player->getName(), $player->getTeamBuilderLink()], null, 'A' . $currentRow);
            $sheet->getCell('B' . $currentRow)->getHyperlink()->setUrl( $player->getTeamBuilderLink());
        }
    }
}
