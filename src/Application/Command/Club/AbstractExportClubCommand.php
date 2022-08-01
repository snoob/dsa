<?php

declare(strict_types=1);

namespace App\Application\Command\Club;

use App\Application\Command\CommandOutputStyle;
use App\Domain\Club\Club;
use App\Domain\Club\ClubProvider;
use App\Domain\Club\Exception\PlayerAlreadyExistsException;
use App\Domain\Club\Exception\PlayerNotExistsException;
use App\Domain\Player\Player;
use App\Domain\Player\PlayerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

abstract class AbstractExportClubCommand extends Command
{
    protected string $exportDir;
    private ClubProvider $clubProvider;

    private PlayerProvider $playerProvider;

    private string $clubId;

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
        string $clubId,
        string $exportDir,
        array $extraPlayersToFetch,
        array $extraPlayersToIgnore
    ) {
        parent::__construct();
        $this->clubProvider = $clubProvider;
        $this->playerProvider = $playerProvider;
        $this->clubId = $clubId;
        $this->exportDir = $exportDir;
        $this->extraPlayersToFetch = $extraPlayersToFetch;
        $this->extraPlayersToIgnore = $extraPlayersToIgnore;
    }

    protected function getClub(CommandOutputStyle $commandOutputStyle): Club
    {
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

        return $club;
    }

    private function getPlayer(CommandOutputStyle $commandOutputStyle, string $playerId, string $extraMessage): ?Player
    {
        $player = $this->playerProvider->find($playerId);

        if (null === $player) {
            $commandOutputStyle->playerNotFound($playerId, $extraMessage);
        }

        return $player;
    }
}
