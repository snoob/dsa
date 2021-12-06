<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\TeamRequirement;
use App\Domain\Toon\ToonTagEnum;

final class Tier7 extends AbstractMode
{
    public function __construct(int $teamSize, ?ToonTagEnum $tag = null)
    {
        parent::__construct(new TeamRequirement($teamSize, $tag, 5, 6));
    }

    public function __toString(): string
    {
        return 'P7';
    }
}
