<?php

declare(strict_types=1);

namespace App\Application\Command\Club;

use App\Application\Command\CommandOutputStyle;
use App\Application\Spreadsheet\AlexTheme;
use App\Application\Translation\Translator;
use App\Domain\Club\ClubExport;
use App\Domain\Club\ClubProvider;
use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\Exploration\Tier8;
use App\Domain\GameMode\Exploration\Tier9;
use App\Domain\Player\Player;
use App\Domain\Player\PlayerProvider;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonProgressProvider;
use App\Domain\Toon\ToonProvider;
use Elao\Enum\Exception\InvalidValueException;
use JetBrains\PhpStorm\Pure;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'club:export:expedition',
    description: 'Export expedition club data.',
)]
final class ExportClubExpeditionCommand extends AbstractExportClubCommand
{
    private const DATE_FORMAT = 'd/m H:i';

    private ToonProvider $toonProvider;

    private ToonProgressProvider $toonProgressProvider;

    private Translator $translator;

    private UrlGeneratorInterface $urlGenerator;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ClubProvider $clubProvider,
        PlayerProvider $playerProvider,
        string $clubId,
        string $exportDir,
        array $extraPlayersToFetch,
        array $extraPlayersToIgnore,
        ToonProvider $toonProvider,
        ToonProgressProvider $toonProgressProvider,
        Translator $translator,
        UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct(
            $clubProvider,
            $playerProvider,
            $clubId,
            $exportDir,
            $extraPlayersToFetch,
            $extraPlayersToIgnore
        );
        $this->toonProvider = $toonProvider;
        $this->toonProgressProvider = $toonProgressProvider;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('tag', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandOutputStyle = new CommandOutputStyle($input, $output);
        $stopwatch = new Stopwatch(true);

        try {
            $tag = TagEnum::get($input->getArgument('tag'));
        } catch (InvalidValueException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }

        $stopwatch->start($tag->getValue());
        $toons = $this->toonProvider->findByTag($tag);
        $gameModes = self::getGameModesToCheck($this->toonProvider->getTeamSizeByTag($tag), $tag);
        $clubExport = new ClubExport($this->getClub($commandOutputStyle), $tag, $gameModes);
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $this->appendHeaderRows($sheet, $gameModes);
        $this->appendContent($sheet, $clubExport, $toons);
        $this->appendSummary($sheet, $clubExport);

        $theme = new AlexTheme();
        $theme->applyTheme($spreadsheet);

        $xlsx = new Xlsx($spreadsheet);
        $filename = $tag->toFilename($this->translator);
        $filePath = sprintf('%s/%s.xlsx', $this->exportDir, $filename);
        $xlsx->save($filePath);
        $stopwatch->stop($tag->getValue());
        $output->writeln(sprintf('%s written in %d sec', $filePath, $stopwatch->getEvent($tag->getValue())->getDuration() / 1000));
        $output->writeln(sprintf('Memory usage %d MB', $stopwatch->getEvent($tag->getValue())->getMemory() / 1024 / 1024));

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
                $clubExport->addEligiblePlayer($gameMode, $player, $teamsCount);
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
            $sheet->setCellValue($eligiblePlayersColumn . '2', $clubExport->getEligiblePlayersLabel($gameMode));
            foreach (array_keys($clubExport->getEligiblePlayers($gameMode)) as $index => $playerName) {
                $sheet->setCellValue($eligiblePlayersColumn . (4 + $index), $clubExport->getPlayerEligibilityLabel($gameMode, $playerName));
            }
            ++$eligiblePlayersColumn;
        }
    }

    /**
     * @return array<int, AbstractMode>
     */
    #[Pure]
    private static function getGameModesToCheck(int $teamSize, TagEnum $tag): array
    {
        return [
            new Tier8($teamSize, $tag),
            new Tier9($teamSize, $tag),
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function setCellLink(Worksheet $sheet, string $coordinate, string $route, array $parameters): void
    {
        $sheet->getCell($coordinate)->getHyperlink()->setUrl(
            $this->urlGenerator->generate(
                sprintf('dsa_fan_%s', $route),
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
