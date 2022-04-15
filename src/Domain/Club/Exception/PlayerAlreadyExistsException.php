<?php

declare(strict_types=1);

namespace App\Domain\Club\Exception;

use App\Domain\Player\Player;
use JetBrains\PhpStorm\Pure;

final class PlayerAlreadyExistsException extends \LogicException
{
    #[Pure]
    public function __construct(Player $player, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Player with id %s already exists in your club', $player->getId()),
            $code,
            $previous
        );
    }
}
