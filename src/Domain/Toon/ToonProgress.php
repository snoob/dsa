<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Application\Cache\CacheableInterface;
use App\Domain\Player\Player;
use JetBrains\PhpStorm\Pure;

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

    public function isUnlocked(): bool
    {
        return $this->star > 0;
    }

    public function getScore(): int
    {
        return $this->star * 10 + $this->gear;
    }

    #[Pure]
    public static function generateCacheId(Player $player, Toon $toon): string
    {
        return sprintf('%s-%s', $player->getId(), $toon->getId());
    }

    public static function generateCacheKey(string $id): string
    {
        return sprintf('toon_progress.%s', $id);
    }
}
