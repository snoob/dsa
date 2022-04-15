<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Domain\Common\AbstractProvider;
use App\Domain\Player\Player;
use Symfony\Contracts\Cache\ItemInterface;

final class ToonProvider extends AbstractProvider
{
    protected const CACHE_EXPIRATION = '1 year';

    /**
     * @return array<int, Toon>
     */
    public function findByTag(TagEnum $tag): array
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

    public function getTeamSizeByTag(TagEnum $tag): int
    {
        return min(\count($this->findByTag($tag)), Player::TEAM_SIZE);
    }

    /**
     * @return array<int, Toon>
     */
    private function getToons(): array
    {
        return [
            new Toon('aladdin', [TagEnum::ALADDIN()]),
            new Toon('ariel', [TagEnum::OCEANIC()]),
            new Toon('barley', [TagEnum::ADVENTURER(), TagEnum::ONWARD()]),
            new Toon('bo-peep', [TagEnum::ADVENTURER()]),
            new Toon('captain-gantu', [TagEnum::OCEANIC()]),
            new Toon('captain-hook', [TagEnum::PETER_PAN(), TagEnum::OCEANIC()]),
            new Toon('cobra-bubbles', [TagEnum::OCEANIC()]),
            new Toon('chip', [TagEnum::ADVENTURER()]),
            new Toon('dale', [TagEnum::ADVENTURER()]),
            new Toon('dash', [TagEnum::THE_INCREDIBLES()]),
            new Toon('davy-jones', [TagEnum::OCEANIC()]),
            new Toon('demona', [TagEnum::GARGOYLES()]),
            new Toon('elastigirl', [TagEnum::THE_INCREDIBLES()]),
            new Toon('flynn-rider', [TagEnum::TANGLED()]),
            new Toon('frank-wolff', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
            new Toon('frozone', [TagEnum::THE_INCREDIBLES()]),
            new Toon('gadget', [TagEnum::ADVENTURER()]),
            new Toon('genie', [TagEnum::ALADDIN()]),
            new Toon('goliath', [TagEnum::GARGOYLES()]),
            new Toon('ian', [TagEnum::ONWARD()]),
            new Toon('jack-jack', [TagEnum::THE_INCREDIBLES()]),
            new Toon('jack-sparrow', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
            new Toon('jafar', [TagEnum::ALADDIN()]),
            new Toon('jasmine', [TagEnum::ALADDIN()]),
            //new Toon('kida', [TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
            new Toon('king-triton', [TagEnum::OCEANIC()]),
            new Toon('lily-houghton', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
            new Toon('maui', [TagEnum::OCEANIC()]),
            new Toon('maximus', [TagEnum::TANGLED()]),
            new Toon('milo-thatch', [TagEnum::ADVENTURER(), TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
            new Toon('moana', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
            new Toon('monterey-jack', [TagEnum::ADVENTURER()]),
            new Toon('mother-gothel', [TagEnum::TANGLED()]),
            new Toon('mr-incredible', [TagEnum::THE_INCREDIBLES()]),
            new Toon('peter-pan', [TagEnum::PETER_PAN(), TagEnum::OCEANIC()]),
            new Toon('prince-eric', [TagEnum::OCEANIC()]),
            new Toon('rapunzel', [TagEnum::TANGLED()]),
            new Toon('raya', [TagEnum::ADVENTURER()]),
            new Toon('smee', [TagEnum::PETER_PAN(), TagEnum::OCEANIC()]),
            new Toon('stitch', [TagEnum::OCEANIC()]),
            new Toon('syndrome', [TagEnum::THE_INCREDIBLES()]),
            new Toon('the-manticore', [TagEnum::ONWARD()]),
            new Toon('tinker-bell', [TagEnum::PETER_PAN(), TagEnum::OCEANIC()]),
            new Toon('ursula', [TagEnum::OCEANIC()]),
            new Toon('violet', [TagEnum::THE_INCREDIBLES()]),
            new Toon('wendy', [TagEnum::PETER_PAN(), TagEnum::OCEANIC()]),
            new Toon('xanatos', [TagEnum::GARGOYLES()]),
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
