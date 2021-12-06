<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Application\DsaApiWrapper;
use App\Domain\Common\AbstractProvider;
use App\Domain\Player\Player;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ToonProgressProvider extends AbstractProvider
{
    private DsaApiWrapper $dsaApiWrapper;

    public function __construct(TagAwareCacheInterface $cache, DsaApiWrapper $dsaApiWrapper)
    {
        parent::__construct($cache);
        $this->dsaApiWrapper = $dsaApiWrapper;
    }

    public function find(Player $player, Toon $toon): ToonProgress
    {
        $toonProgress = $this->cache->get(sprintf('player.%s.toon.%s', $player->getId(), $toon->getId()), function (ItemInterface $item) use ($player, $toon) {
            $item->tag(['player', 'player.%s']);
            $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

            return $this->dsaApiWrapper->getToonProgress($player, $toon);
        });
        $player->addToon($toonProgress);

        return $toonProgress;
    }
}
