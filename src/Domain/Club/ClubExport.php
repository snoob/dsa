<?php

declare(strict_types=1);

namespace App\Domain\Club;

use App\Domain\GameMode\AbstractMode;
use App\Domain\Player\Player;
use App\Domain\Toon\TagEnum;

final class ClubExport
{
    private Club $club;

    private TagEnum $tag;

    /**
     * @var array<int, AbstractMode>
     */
    private array $gameModes;

    /**
     * @var array<string, array<string, float>>
     */
    private array $playersEligibility;

    /**
     * @param array<int, AbstractMode> $gameModes
     */
    public function __construct(Club $club, TagEnum $tag, array $gameModes)
    {
        $this->club = $club;
        $this->tag = $tag;
        $this->gameModes = $gameModes;
        $this->playersEligibility = [];
    }

    public function getTag(): TagEnum
    {
        return $this->tag;
    }

    public function getClub(): Club
    {
        return $this->club;
    }

    /**
     * @return array<int, AbstractMode>
     */
    public function getGameModes(): array
    {
        return $this->gameModes;
    }

    /**
     * @return array<int, Player>
     */
    public function getEligiblePlayers(AbstractMode $abstractMode): array
    {
        return $this->playersEligibility[\get_class($abstractMode)] ?? [];
    }

    public function getEligiblePlayersLabel(AbstractMode $abstractMode): string
    {
        $playerCount = 0;
        $teamCount = 0;

        foreach ($this->playersEligibility[\get_class($abstractMode)] ?? [] as $playerTeamCount) {
            $teamCount += (int) $playerTeamCount;
            ++$playerCount;
        }

        if ($teamCount === $playerCount) {
            return (string) $playerCount;
        }

        return sprintf('%d (%d)', $playerCount, $teamCount);
    }

    public function addEligiblePlayer(AbstractMode $abstractMode, Player $player, float $teamCount): void
    {
        $this->playersEligibility[\get_class($abstractMode)][$player->getName()] = $teamCount;
    }

    public function getPlayerEligibilityLabel(AbstractMode $abstractMode, string $playerName): string
    {
        $teamCount = $this->playersEligibility[\get_class($abstractMode)][$playerName];

        if ($teamCount < 2) {
            return $playerName;
        }

        return sprintf('%s (%d)', $playerName, $teamCount);
    }
}
