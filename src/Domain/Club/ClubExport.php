<?php

declare(strict_types=1);

namespace App\Domain\Club;

use App\Domain\GameMode\AbstractMode;
use App\Domain\Player\Player;
use App\Domain\Toon\ToonTagEnum;

final class ClubExport
{
    private Club $club;

    private ToonTagEnum $tag;

    /**
     * @var array<int, AbstractMode>
     */
    private array $gameModes;

    /**
     * @var array<string, array<int, Player>>
     */
    private array $playersEligibility;

    /**
     * @param array<int, AbstractMode> $gameModes
     */
    public function __construct(Club $club, ToonTagEnum $tag, array $gameModes)
    {
        $this->club = $club;
        $this->tag = $tag;
        $this->gameModes = $gameModes;
        $this->playersEligibility = [];
    }

    public function getTag(): ToonTagEnum
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

    public function addEligiblePlayer(AbstractMode $abstractMode, Player $player): void
    {
        $this->playersEligibility[\get_class($abstractMode)][] = $player;
    }
}
