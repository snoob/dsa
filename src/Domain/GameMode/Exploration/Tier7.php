<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\Toon\TagEnum;
use JetBrains\PhpStorm\Pure;

final class Tier7 extends AbstractExplorationMode
{
    #[Pure]
    public function __construct(int $teamSize, ?TagEnum $tag)
    {
        parent::__construct($teamSize, $tag, 5, 6);
    }

    public function __toString(): string
    {
        return 'P7';
    }
}
