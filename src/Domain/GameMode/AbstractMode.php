<?php

declare(strict_types=1);

namespace App\Domain\GameMode;

use App\Domain\Player\Player;
use App\Domain\Toon\ToonProgress;

abstract class AbstractMode implements RequirementInterface, \Stringable
{
    private RequirementInterface $requirement;

    public function __construct(RequirementInterface $requirement)
    {
        $this->requirement = $requirement;
    }

    abstract public function __toString(): string;

    public function isPlayerEligible(Player $player): float
    {
        return $this->requirement->isPlayerEligible($player);
    }

    public function isToonEligible(ToonProgress $toonProgress): bool
    {
        return $this->requirement->isToonEligible($toonProgress);
    }
}
