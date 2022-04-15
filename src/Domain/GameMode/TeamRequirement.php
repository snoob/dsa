<?php

declare(strict_types=1);

namespace App\Domain\GameMode;

use App\Domain\Player\Player;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\ToonProgress;
use JetBrains\PhpStorm\Pure;

final class TeamRequirement implements RequirementInterface
{
    private int $teamSize;

    private ?TagEnum $tag;

    private int $minStar;

    private int $minGear;

    public function __construct(int $teamSize, ?TagEnum $tag = null, int $minStar = 0, int $minGear = 0)
    {
        $this->teamSize = $teamSize;
        $this->tag = $tag;
        $this->minStar = $minStar;
        $this->minGear = $minGear;
    }

    public function isPlayerEligible(Player $player): float
    {
        $toons = null !== $this->tag ? $this->buildToonMap($player)[$this->tag->getValue()] ?? [] : $player->getToons();

        $eligibleToons = 0;
        foreach ($toons as $toon) {
            if ($toon->getStar() >= $this->minStar && $toon->getGear() >= $this->minGear) {
                ++$eligibleToons;
            }
        }

        return $eligibleToons / $this->teamSize;
    }

    #[Pure]
    public function isToonEligible(ToonProgress $toonProgress): bool
    {
        // @TODO add tag check later when toons will be not pre-filtered
        return $toonProgress->getStar() >= $this->minStar && $toonProgress->getGear() >= $this->minGear;
    }

    /**
     * @return array<string, array<int, ToonProgress>>
     */
    #[Pure]
    private function buildToonMap(Player $player): array
    {
        // @TODO array_filter
        $toonMap = [];

        foreach ($player->getToons() as $toonProgress) {
            foreach ($toonProgress->getToon()->getTags() as $toon) {
                $toonMap[$toon->getValue()][] = $toonProgress;
            }
        }

        return $toonMap;
    }
}
