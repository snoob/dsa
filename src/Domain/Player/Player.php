<?php

declare(strict_types=1);

namespace App\Domain\Player;

use App\Application\Cache\CacheableInterface;
use App\Domain\Toon\ToonProgress;
use JetBrains\PhpStorm\Pure;

final class Player implements \Stringable, CacheableInterface
{
    public const TEAM_SIZE = 5;

    private string $id;

    private string $name;

    private \DateTimeImmutable $lastUpdatedDate;

    /**
     * @var array<string, ToonProgress>
     */
    private array $toons;

    public function __construct(string $id, string $name, \DateTimeImmutable $lastUpdatedDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastUpdatedDate = $lastUpdatedDate;
        $this->toons = [];
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastUpdatedDate(): \DateTimeImmutable
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @return array<string, ToonProgress>
     */
    public function getToons(): array
    {
        return $this->toons;
    }

    public function addToon(ToonProgress $toonProgress): void
    {
        $this->toons[$toonProgress->getToon()->getId()] = $toonProgress;
    }

    public function getTeamBuilderLink(): string
    {
        return sprintf('https://script.google.com/macros/s/AKfycbw7csLy2GMHaKaP69CbzOwBGyW7fyrr8wbYZBvMzd8bvvnREB4cR7ifi_wcmTJ-F8I/exec?playerID=%s', $this->id);
    }

    #[Pure]
    public function equals(Player $player): bool
    {
        return $this->id === $player->getId();
    }

    /**
     * @TODO unused for now we need to implement cache tag strategy
     *
     * @return array<int, string>
     */
    public static function getCacheTags(string $id): array
    {
        return ['player', sprintf('player.%s', $id)];
    }

    public static function generateCacheKey(string $id): string
    {
        return sprintf('player.%s', $id);
    }
}
