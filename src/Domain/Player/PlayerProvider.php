<?php

declare(strict_types=1);

namespace App\Domain\Player;

use App\Application\DsaApiWrapper;
use App\Domain\Club\Club;
use App\Domain\Common\AbstractProvider;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class PlayerProvider extends AbstractProvider
{
    private DsaApiWrapper $dsaApiWrapper;

    #[Pure]
    public function __construct(TagAwareCacheInterface $cache, DsaApiWrapper $dsaApiWrapper)
    {
        parent::__construct($cache);
        $this->dsaApiWrapper = $dsaApiWrapper;
        $this->cache = $cache;
    }

    /**
     * @return array<string, Player>
     */
    public function findByClub(Club $club): array
    {
        $players = [];

        foreach ($this->dsaApiWrapper->getClubPlayerIds($club) as $playerId) {
            $players[$playerId] = $this->find($playerId);
        }

        return $players;
    }

    public function find(string $id): ?Player
    {
        return $this->cache->get(Player::generateCacheKey($id), function (ItemInterface $item) use ($id) {
            $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

            return $this->dsaApiWrapper->getPlayer($id);
        });
    }
}
