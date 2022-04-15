<?php

declare(strict_types=1);

namespace App\Domain\Club;

use App\Application\Cache\CacheableInterface;
use App\Domain\Player\Player;
use JetBrains\PhpStorm\Pure;

final class Club implements \Stringable, CacheableInterface
{
    private string $id;

    private string $name;

    private \DateTimeImmutable $lastUpdatedDate;

    /**
     * @var array<int, Player>
     */
    private array $players;

    /**
     * @param array<int, Player> $players
     */
    public function __construct(string $id, string $name, \DateTimeImmutable $lastUpdatedDate, array $players)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastUpdatedDate = $lastUpdatedDate;
        $this->players = $players;
    }

    #[Pure]
    public function __toString(): string
    {
        return ucfirst($this->getName());
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
     * @return array<int, Player>
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public static function generateCacheKey(string $id): string
    {
        return sprintf('club.%s', $id);
    }
}
