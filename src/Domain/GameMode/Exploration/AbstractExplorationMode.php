<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\TeamRequirement;
use App\Domain\Toon\TagEnum;
use JetBrains\PhpStorm\Pure;

abstract class AbstractExplorationMode extends AbstractMode
{
    #[Pure]
    public function __construct(int $teamSize, ?TagEnum $tag, int $minStar, int $minGear)
    {
        parent::__construct(new TeamRequirement($teamSize, $tag, $minStar, $minGear));
    }
}
