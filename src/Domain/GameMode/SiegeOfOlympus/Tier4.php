<?php

declare(strict_types=1);

namespace App\Domain\GameMode\SiegeOfOlympus;

use App\Domain\Toon\TagEnum;
use JetBrains\PhpStorm\Pure;

final class Tier4 extends AbstractSiegeOfOlympusMode
{
    #[Pure]
    public function __construct(int $teamSize)
    {
        parent::__construct($teamSize, 7, 7);
    }
}
