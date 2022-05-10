<?php

namespace App\Domain\Toon;

use Traversable;

final class ToonList implements \IteratorAggregate
{
    /**
     * @var array<string, Toon>
     */
    private array $toons = [];

    /**
     * @var array<int, Toon>
     */
    public function __construct(array $toons)
    {
        foreach ($toons as $toon) {
            $this->toons[$toon->getId()] = $toon;
        }
    }

    public function filterById(string|array $ids): self
    {
        if (is_string($ids)) {
            $ids = [$ids];
        }

        $filteredToons = [];

        foreach ($ids as $id) {
            $filteredToons[] = $this->toons[$id] ?? null;
        }

        return $this->createFrom(array_filter($filteredToons));
    }

    public function sortByFastest(): self
    {
        $sortedToons = $this->toons;

        usort($sortedToons, static function (Toon $toon1, Toon $toon2) {
            return $toon2->getSpeed() <=> $toon1->getSpeed();
        });

        return $this->createFrom($sortedToons);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->toons);
    }

    /**
     * @param array<int, Toon> $toons
     */
    private function createFrom(array $toons): self
    {
        return new self($toons);
    }

    public static function create(): self
    {
        return new ToonList([]);
    }
}
