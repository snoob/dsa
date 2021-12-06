<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Spreadsheet\AlexTheme;
use App\Domain\Club\ClubExport;
use App\Domain\Club\ClubProvider;
use App\Domain\Common\Translator;
use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\Exploration\Tier6;
use App\Domain\GameMode\Exploration\Tier7;
use App\Domain\Player\Player;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonProgressProvider;
use App\Domain\Toon\ToonProvider;
use App\Domain\Toon\ToonTagEnum;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:export:club',
    description: 'Export your club players data.',
)]
class ExportClubDataCommand extends Command
{
    private const DATE_FORMAT = 'd/m H:i';

    private ClubProvider $clubProvider;

    private ToonProvider $toonProvider;

    private ToonProgressProvider $toonProgressProvider;

    private Translator $translator;

    private UrlGeneratorInterface $urlGenerator;

    private string $clubId;

    private string $exportDir;

    public function __construct(
        ClubProvider $clubProvider,
        ToonProvider $toonProvider,
        ToonProgressProvider $toonProgressProvider,
        Translator $translator,
        UrlGeneratorInterface $urlGenerator,
        string $clubId,
        string $exportDir
    ) {
        $this->clubProvider = $clubProvider;
        $this->toonProvider = $toonProvider;
        $this->toonProgressProvider = $toonProgressProvider;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->clubId = $clubId;
        $this->exportDir = $exportDir;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch(true);

        $club = $this->clubProvider->find($this->clubId);

        if (null === $club) {
            throw new InvalidArgumentException(sprintf('Club with id %s not found.', $this->clubId));
        }

        foreach (self::getToonTags() as $tag) {
            $stopwatch->start($tag->getValue());
            $toons = $this->toonProvider->findByTag($tag);
            $gameModes = self::getGameModesToCheck(\count($toons) >= 5 ? 5 : \count($toons), $tag);
            $clubExport = new ClubExport($club, $tag, $gameModes);
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();
            $this->appendHeaderRows($sheet, $gameModes);
            $this->appendContent($sheet, $clubExport, $toons);
            $this->appendSummary($sheet, $clubExport);

            $theme = new AlexTheme();
            $theme->applyTheme($spreadsheet);

            $xlsx = new Xlsx($spreadsheet);
            $filename = sprintf('%s/%s.xlsx', $this->exportDir, $tag->toFilename($this->translator));
            $xlsx->save($filename);
            $stopwatch->stop($tag->getValue());
            $output->writeln(sprintf('%s written in %s sec', $filename, $stopwatch->getEvent($tag->getValue())->getDuration() / 1000));
        }

        return Command::SUCCESS;
    }

    /**
     * @param array<int, AbstractMode> $gameModes
     */
    private function appendHeaderRows(Worksheet $sheet, array $gameModes): void
    {
        $sheet->fromArray(array_merge($this->getTranslatedHeaders(['toon', 'star', 'gear']), $gameModes), null, 'B1');
    }

    /**
     * @param array<int, Toon> $toons
     */
    private function appendContent(Worksheet $sheet, ClubExport $clubExport, array $toons): void
    {
        $toonProgressRange = [];
        $playerEligibilityRange = [];

        foreach ($clubExport->getClub()->getPlayers() as $player) {
            $currentRow = $sheet->getHighestRow() + 1;
            $playerNameCell = 'A' . $currentRow;
            $sheet->setCellValue($playerNameCell, $player->getName() . PHP_EOL . $player->getLastUpdatedDate()->format(self::DATE_FORMAT));
            $this->setCellLink($sheet, $playerNameCell, 'player', ['id' => $player->getId()]);

            $toonProgressRange[] = $this->appendToonProgress($sheet, $clubExport, $player, $toons);
            $playerEligibilityRange[] = $this->appendPlayerEligibility($sheet, $clubExport, $player);

            $sheet->mergeCells(sprintf('%s:A%s', $playerNameCell, $sheet->getHighestRow()));
        }

        $sheet->getParent()->addNamedRange(new NamedRange('toon_progress', $sheet, implode(',', $toonProgressRange)));
        $sheet->getParent()->addNamedRange(new NamedRange('player_eligibility', $sheet, implode(',', $playerEligibilityRange)));
    }

    /**
     * @param array<int, Toon> $toons
     */
    private function appendToonProgress(Worksheet $sheet, ClubExport $clubExport, Player $player, array $toons): string
    {
        $currentRow = $sheet->getHighestRow();
        $range = sprintf('E%d:F%d', $currentRow, $currentRow + \count($toons) - 1);
        foreach ($toons as $toon) {
            $toonProgress = $this->toonProgressProvider->find($player, $toon);
            if ($toonProgress->isUnlocked()) {
                $this->setCellLink($sheet, 'B' . $currentRow, 'toon_progress', ['playerId' => $player->getId(), 'id' => $toon->getId()]);
            }

            $row = [$this->translator->trans($toonProgress->getToon()), $toonProgress->getStar(), $toonProgress->getGear()];
            foreach ($clubExport->getGameModes() as $gameMode) {
                $row[] = $gameMode->isToonEligible($toonProgress) ? 'OK' : 'NOK';
            }
            $sheet->fromArray($row, null, 'B' . $currentRow);
            ++$currentRow;
        }

        return $range;
    }

    private function appendPlayerEligibility(Worksheet $sheet, ClubExport $clubExport, Player $player): string
    {
        $currentRow = $sheet->getHighestRow() + 1;
        $playerEligibilityRow = [$this->translator->trans('row.is_eligible'), '', ''];

        foreach ($clubExport->getGameModes() as $gameMode) {
            $teamsCount = $gameMode->isPlayerEligible($player);
            $playerEligibilityRow[] = (string) $teamsCount;
            if ($teamsCount >= 1) {
                $clubExport->addEligiblePlayer($gameMode, $player);
            }
        }

        $sheet->fromArray($playerEligibilityRow, null, sprintf('B%d', $currentRow));

        return sprintf('E%1$d,F%1$d', $currentRow);
    }

    private function appendSummary(Worksheet $sheet, ClubExport $clubExport): void
    {
        $club = $clubExport->getClub();

        $sheet->fromArray(array_merge($this->getTranslatedHeaders(['date', 'club', 'members', 'tag']), $clubExport->getGameModes()), null, 'H1');
        $sheet->fromArray(
            [
                $club->getLastUpdatedDate()->format(self::DATE_FORMAT),
                $club,
                \count($club->getPlayers()),
                $this->translator->trans($clubExport->getTag()), ],
            null,
            'H2'
        );
        $this->setCellLink($sheet, 'I2', 'club', ['id' => $club->getId()]);
        $this->setCellLink($sheet, 'K2', 'toon_list', ['category' => $clubExport->getTag()->getValue()]);

        $eligiblePlayersColumn = 'L';
        foreach ($clubExport->getGameModes() as $gameMode) {
            $sheet->setCellValue($eligiblePlayersColumn . '2', \count($clubExport->getEligiblePlayers($gameMode)));
            foreach ($clubExport->getEligiblePlayers($gameMode) as $index => $player) {
                $sheet->setCellValue($eligiblePlayersColumn . (4 + $index), $player);
            }
            ++$eligiblePlayersColumn;
        }
    }

    /**
     * @return array<int, ToonTagEnum>
     */
    private static function getToonTags(): array
    {
        return ToonTagEnum::instances();
    }

    /**
     * @return array<int, AbstractMode>
     */
    private static function getGameModesToCheck(int $teamSize, ToonTagEnum $tag): array
    {
        return [
            new Tier6($teamSize, $tag),
            new Tier7($teamSize, $tag),
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function setCellLink(Worksheet $sheet, string $coordinate, string $route, array $parameters): void
    {
        $sheet->getCell($coordinate)->getHyperlink()->setUrl(
            $this->urlGenerator->generate(
                sprintf('dsa_%s', $route),
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }

    /**
     * @param array<int, string> $headers
     *
     * @return array<int, string>
     */
    private function getTranslatedHeaders(array $headers): array
    {
        $translatedHeaders = [];

        foreach ($headers as $header) {
            $translatedHeaders[] = $this->translator->trans(sprintf('column.%s', $header));
        }

        return $translatedHeaders;
    }
}
