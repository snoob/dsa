<?php

declare(strict_types=1);

namespace App\Domain\GameMode\SiegeOfOlympus;

use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\TeamRequirement;
use App\Domain\Toon\TagEnum;
use JetBrains\PhpStorm\Pure;

abstract class AbstractSiegeOfOlympusMode extends AbstractMode
{
    protected int $teamSize;

    #[Pure]
    public function __construct(int $teamSize,int $minStar, int $minGear)
    {
        parent::__construct(new TeamRequirement($teamSize, TagEnum::CHOSEN(), $minStar, $minGear));
        $this->teamSize = $teamSize;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s choisis)', $this->getModeLabel(), $this->teamSize);
    }

    protected function getModeLabel(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }
}
