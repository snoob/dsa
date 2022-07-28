<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Translation\Translator;
use App\Domain\Club\Club;
use App\Domain\Club\ClubProvider;
use App\Domain\Club\Exception\PlayerAlreadyExistsException;
use App\Domain\Club\Exception\PlayerNotExistsException;
use App\Domain\Player\Player;
use App\Domain\Player\PlayerProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:export:team-builder',
    description: 'Export your club players data.',
)]
final class ExportTeamBuilderCommand extends Command
{
    private ClubProvider $clubProvider;

    private PlayerProvider $playerProvider;

    private Translator $translator;

    private string $clubId;

    private string $exportDir;

    /**
     * @var array<int, string>
     */
    private array $extraPlayersToFetch;

    /**
     * @var array<int, string>
     */
    private array $extraPlayersToIgnore;

    /**
     * @param array<int, string> $extraPlayersToFetch
     * @param array<int, string> $extraPlayersToIgnore
     */
    public function __construct(
        ClubProvider $clubProvider,
        PlayerProvider $playerProvider,
        Translator $translator,
        string $clubId,
        string $exportDir,
        array $extraPlayersToFetch,
        array $extraPlayersToIgnore
    ) {
        parent::__construct();
        $this->clubProvider = $clubProvider;
        $this->playerProvider = $playerProvider;
        $this->translator = $translator;
        $this->clubId = $clubId;
        $this->exportDir = $exportDir;
        $this->extraPlayersToFetch = $extraPlayersToFetch;
        $this->extraPlayersToIgnore = $extraPlayersToIgnore;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandOutputStyle = new CommandOutputStyle($input, $output);
        $stopwatch = new Stopwatch(true);
        $stopwatch->start($this->getName());

        $club = $this->clubProvider->find($this->clubId);

        if (null === $club) {
            throw new InvalidArgumentException(sprintf('Club with id %s not found.', $this->clubId));
        }

        foreach ($this->playerProvider->findByClub($club) as $playerId => $player) {
            if (null === $player) {
                $commandOutputStyle->playerNotFound($playerId, 'Id was crawled from club page');

                continue;
            }
            $club->addPlayer($player);
        }

        foreach ($this->extraPlayersToFetch as $playerId) {
            $player = $this->getPlayer($commandOutputStyle, $playerId, 'You can remove it from EXTRA_PLAYERS_TO_FETCH ENV variable');
            if (null === $player) {
                continue;
            }

            try {
                $club->addPlayer($player);
            } catch (PlayerAlreadyExistsException $exception) {
                $commandOutputStyle->warning(sprintf('%s : You can remove it from EXTRA_PLAYERS_TO_FETCH ENV variable', $exception->getMessage()));
            }
        }

        foreach ($this->extraPlayersToIgnore as $playerId) {
            $player = $this->getPlayer($commandOutputStyle, $playerId, 'You can remove it from EXTRA_PLAYERS_TO_IGNORE ENV variable');
            if (null === $player) {
                continue;
            }

            try {
                $club->removePlayer($player);
            } catch (PlayerNotExistsException $exception) {
                $commandOutputStyle->warning(sprintf('%s : You can remove it from EXTRA_PLAYERS_TO_IGNORE ENV variable', $exception->getMessage()));
            }
        }

        $club->sortPlayers();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->appendHeaderRows($sheet);
        $this->appendContent($sheet, $club);

        $xlsx = new Xlsx($spreadsheet);
        $filePath = sprintf('%s/%s.xlsx', $this->exportDir, 'team-builder');
        $xlsx->save($filePath);
        $output->writeln(sprintf('%s written in %d sec', $filePath, $stopwatch->getEvent($this->getName())->getDuration() / 1000));
        $output->writeln(sprintf('Memory usage %d MB', $stopwatch->getEvent($this->getName())->getMemory() / 1024 / 1024));

        return Command::SUCCESS;
    }

    private function appendHeaderRows(Worksheet $sheet): void
    {
        $sheet->fromArray($this->getTranslatedHeaders(['Joueur', 'Lien']));
    }

    private function appendContent(Worksheet $sheet, Club $club): void
    {
        foreach ($club->getPlayers() as $player) {
            $currentRow = $sheet->getHighestRow() + 1;
            $sheet->fromArray([$player->getName(), $player->getTeamBuilderLink()], null, 'A' . $currentRow);
        }
    }

    private function getPlayer(CommandOutputStyle $commandOutputStyle, string $playerId, string $extraMessage): ?Player
    {
        $player = $this->playerProvider->find($playerId);

        if (null === $player) {
            $commandOutputStyle->playerNotFound($playerId, $extraMessage);
        }

        return $player;
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
