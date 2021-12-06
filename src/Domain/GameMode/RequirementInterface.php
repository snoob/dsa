<?php

declare(strict_types=1);

namespace App\Domain\GameMode;

use App\Domain\Player\Player;
use App\Domain\Toon\ToonProgress;

interface RequirementInterface
{
    public function isPlayerEligible(Player $player): float;

    public function isToonEligible(ToonProgress $toonProgress): bool;
}
