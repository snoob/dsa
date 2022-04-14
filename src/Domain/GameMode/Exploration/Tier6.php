<?php

declare(strict_types=1);

namespace App\Domain\GameMode\Exploration;

use App\Domain\Toon\TagEnum;

final class Tier6 extends AbstractExplorationMode
{
    public function __construct(int $teamSize, ?TagEnum $tag)
    {
        parent::__construct($teamSize, $tag, 4, 5);
    }

    public function __toString(): string
    {
        return 'P6';
    }
}
