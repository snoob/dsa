<?php

declare(strict_types=1);

namespace App\Domain\Player;

use App\Domain\Toon\ToonProgress;

final class Player implements \Stringable
{
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
}
