<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\Player\Player;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\ToonProvider;

final class ExplorationModeFactory
{
    private ToonProvider $toonProvider;

    public function __construct(ToonProvider $toonProvider)
    {
        $this->toonProvider = $toonProvider;
    }

    public function create(int $tier, ?TagEnum $tag): AbstractExplorationMode
    {
        $teamSize = null === $tag ? Player::TEAM_SIZE : $this->toonProvider->getTeamSizeByTag($tag);

        switch ($tier) {
            case 6:
                return new Tier6($teamSize, $tag);

            case 7:
                return new Tier7($teamSize, $tag);

            case 8:
                return new Tier8($teamSize, $tag);

            case 9:
                return new Tier9($teamSize, $tag);

            default:
                throw new \LogicException('Exploration tier must be between 6 and 9');
        }
    }
}
