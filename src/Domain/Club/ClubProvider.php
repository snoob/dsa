<?php

declare(strict_types=1);

namespace App\Domain\Club;

use App\Application\DsaFanApiWrapper;
use App\Domain\Common\AbstractProvider;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ClubProvider extends AbstractProvider
{
    private DsaFanApiWrapper $DsaFanApiWrapper;

    #[Pure]
    public function __construct(TagAwareCacheInterface $cache, DsaFanApiWrapper $DsaFanApiWrapper)
    {
        parent::__construct($cache);
        $this->DsaFanApiWrapper = $DsaFanApiWrapper;
        $this->cache = $cache;
    }

    public function find(string $id): ?Club
    {
        return $this->cache->get(Club::generateCacheKey($id), function (ItemInterface $item) use ($id) {
            $item->tag('club');
            $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

            return $this->DsaFanApiWrapper->getClub($id);
        });
    }
}
