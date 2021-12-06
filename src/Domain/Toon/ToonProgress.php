<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Domain\Common\CacheableInterface;
use App\Domain\Player\Player;

final class ToonProgress implements CacheableInterface
{
    private Player $player;

    private Toon $toon;

    private int $star;

    private int $gear;

    public function __construct(Player $player, Toon $toon, int $star, int $gear, int $level)
    {
        $this->player = $player;
        $this->toon = $toon;
        $this->star = $star;
        $this->gear = $gear;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getToon(): Toon
    {
        return $this->toon;
    }

    public function getStar(): int
    {
        return $this->star;
    }

    public function getGear(): int
    {
        return $this->gear;
    }

    public function getCacheKey(): string
    {
        return sprintf('player.%s.toon.%s', $this->player->getId(), $this->toon->getId());
    }

    public function isUnlocked(): bool
    {
        return $this->star > 0;
    }
}
