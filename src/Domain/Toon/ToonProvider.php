<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Domain\Common\AbstractProvider;
use Symfony\Contracts\Cache\ItemInterface;

final class ToonProvider extends AbstractProvider
{
    protected const CACHE_EXPIRATION = '1 year';

    /**
     * @return array<int, Toon>
     */
    public function findByTag(ToonTagEnum $tag): array
    {
        return $this->cache->get($tag->getCacheKey(), function () use ($tag) {
            $toonMap = $this->buildToonMap();
            foreach ($toonMap as $toons) {
                $this->cache->get($tag->getCacheKey(), function (ItemInterface $item) use ($toons) {
                    $item->tag('toon');
                    $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

                    return $toons;
                });
            }

            return $toonMap[$tag->getValue()] ?? [];
        });
    }

    /**
     * @return array<int, Toon>
     */
    private function getToons(): array
    {
        return [
            new Toon('aladdin', [ToonTagEnum::ALADDIN()]),
            new Toon('barley', [ToonTagEnum::ADVENTURER(), ToonTagEnum::ONWARD()]),
            new Toon('bo-peep', [ToonTagEnum::ADVENTURER()]),
            new Toon('captain-hook', [ToonTagEnum::PETER_PAN()]),
            new Toon('chip', [ToonTagEnum::ADVENTURER()]),
            new Toon('dale', [ToonTagEnum::ADVENTURER()]),
            new Toon('dash', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('demona', [ToonTagEnum::GARGOYLES()]),
            new Toon('elastigirl', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('flynn-rider', [ToonTagEnum::TANGLED()]),
            new Toon('frank-wolff', [ToonTagEnum::ADVENTURER(), ToonTagEnum::JUNGLE_CRUISE()]),
            new Toon('frozone', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('gadget', [ToonTagEnum::ADVENTURER()]),
            new Toon('genie', [ToonTagEnum::ALADDIN()]),
            new Toon('goliath', [ToonTagEnum::GARGOYLES()]),
            new Toon('ian', [ToonTagEnum::ONWARD()]),
            new Toon('jack-jack', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('jack-sparrow', [ToonTagEnum::ADVENTURER()]),
            new Toon('jafar', [ToonTagEnum::ALADDIN()]),
            new Toon('jasmine', [ToonTagEnum::ALADDIN()]),
            new Toon('kida', [ToonTagEnum::ATLANTIS()]),
            new Toon('lily-houghton', [ToonTagEnum::ADVENTURER(), ToonTagEnum::JUNGLE_CRUISE()]),
            new Toon('maximus', [ToonTagEnum::TANGLED()]),
            new Toon('milo-thatch', [ToonTagEnum::ADVENTURER(), ToonTagEnum::ATLANTIS()]),
            new Toon('moana', [ToonTagEnum::ADVENTURER()]),
            new Toon('monterey-jack', [ToonTagEnum::ADVENTURER()]),
            new Toon('mother-gothel', [ToonTagEnum::TANGLED()]),
            new Toon('mr-incredible', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('peter-pan', [ToonTagEnum::PETER_PAN()]),
            new Toon('rapunzel', [ToonTagEnum::TANGLED()]),
            new Toon('raya', [ToonTagEnum::ADVENTURER()]),
            new Toon('smee', [ToonTagEnum::PETER_PAN()]),
            new Toon('syndrome', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('the-manticore', [ToonTagEnum::ONWARD()]),
            new Toon('tinker-bell', [ToonTagEnum::PETER_PAN()]),
            new Toon('violet', [ToonTagEnum::THE_INCREDIBLES()]),
            new Toon('wendy', [ToonTagEnum::PETER_PAN()]),
            new Toon('xanatos', [ToonTagEnum::GARGOYLES()]),
        ];
    }

    /**
     * @return array<string, array<string, Toon>>
     */
    private function buildToonMap(): array
    {
        $toonMap = [];

        foreach ($this->getToons() as $toon) {
            foreach ($toon->getTags() as $toonTag) {
                $toonMap[$toonTag->getValue()][$toon->getId()] = $toon;
            }
        }

        return $toonMap;
    }
}
