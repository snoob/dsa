<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CommandOutputStyle extends SymfonyStyle
{
    public function playerNotFound(string $playerId, string $extraMessage): void
    {
        $this->warning(sprintf('Player with id %s is not retrievable through API : %s', $playerId, $extraMessage));
    }
}
