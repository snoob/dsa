<?php

declare(strict_types=1);

namespace App\Domain\GameMode\SiegeOfOlympus;

use JetBrains\PhpStorm\Pure;

final class Heroic extends AbstractSiegeOfOlympusMode
{
    #[Pure]
    public function __construct(int $teamSize)
    {
        parent::__construct($teamSize, 7, 8);
    }
}
