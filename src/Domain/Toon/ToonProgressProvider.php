<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Application\Http\DsaFanApiWrapper;
use App\Domain\Common\AbstractProvider;
use App\Domain\Player\Player;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ToonProgressProvider extends AbstractProvider
{
    private DsaFanApiWrapper $DsaFanApiWrapper;

    #[Pure]
    public function __construct(TagAwareCacheInterface $cache, DsaFanApiWrapper $DsaFanApiWrapper)
    {
        parent::__construct($cache);
        $this->DsaFanApiWrapper = $DsaFanApiWrapper;
    }

    public function find(Player $player, Toon $toon): ToonProgress
    {
        $cacheId = ToonProgress::generateCacheId($player, $toon);

        $toonProgress = $this->cache->get(ToonProgress::generateCacheKey($cacheId), function (ItemInterface $item) use ($player, $toon) {
            $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

            return $this->DsaFanApiWrapper->getToonProgress($player, $toon);
        });
        $player->addToon($toonProgress);

        return $toonProgress;
    }
}
