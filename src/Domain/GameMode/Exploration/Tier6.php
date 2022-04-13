<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\TeamRequirement;
use App\Domain\Toon\TagEnum;

final class Tier6 extends AbstractMode
{
    public function __construct(int $teamSize, ?TagEnum $tag = null)
    {
        parent::__construct(new TeamRequirement($teamSize, $tag, 4, 5));
    }

    public function __toString(): string
    {
        return 'P6';
    }
}
