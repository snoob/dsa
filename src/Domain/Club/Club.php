<?php

declare(strict_types=1);

namespace App\Domain\Club;

use App\Application\Cache\CacheableInterface;
use App\Domain\Club\Exception\PlayerAlreadyExistsException;
use App\Domain\Club\Exception\PlayerNotExistsException;
use App\Domain\Player\Player;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
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

    public function __construct(string $id, string $name, \DateTimeImmutable $lastUpdatedDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastUpdatedDate = $lastUpdatedDate;
        $this->players = [];
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

    public function addPlayer(Player $player): void
    {
        try {
            $this->getPlayerIndex($player);
        } catch (PlayerNotExistsException) {
            $this->players[] = $player;

            return;
        }

        throw new PlayerAlreadyExistsException($player);
    }

    public function removePlayer(Player $player): void
    {
        unset($this->players[$this->getPlayerIndex($player)]);
    }

    public function sortPlayers(): void
    {
        $collection = new ArrayCollection($this->players);

        $this->players = $collection->matching(Criteria::create()->orderBy(['name' => Criteria::ASC]))->toArray();
    }

    public static function generateCacheKey(string $id): string
    {
        return sprintf('club.%s', $id);
    }

    private function getPlayerIndex(Player $playerToFind): int
    {
        foreach ($this->players as $index => $player) {
            if ($player->equals($playerToFind)) {
                return $index;
            }
        }

        throw new PlayerNotExistsException($playerToFind);
    }
}
