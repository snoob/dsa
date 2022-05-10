<?php

declare(strict_types=1);

namespace App\Domain\Player;

use App\Application\Http\DsaFanApiWrapper;
use App\Domain\Club\Club;
use App\Domain\Common\AbstractProvider;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class PlayerProvider extends AbstractProvider
{
    private DsaFanApiWrapper $DsaFanApiWrapper;

    #[Pure]
    public function __construct(TagAwareCacheInterface $cache, DsaFanApiWrapper $DsaFanApiWrapper)
    {
        parent::__construct($cache);
        $this->DsaFanApiWrapper = $DsaFanApiWrapper;
        $this->cache = $cache;
    }

    /**
     * @return array<string, Player>
     */
    public function findByClub(Club $club): array
    {
        $players = [];

        foreach ($this->DsaFanApiWrapper->getClubPlayerIds($club) as $playerId) {
            $players[$playerId] = $this->find($playerId);
        }

        return $players;
    }

    public function find(string $id): ?Player
    {
        return $this->cache->get(Player::generateCacheKey($id), function (ItemInterface $item) use ($id) {
            $item->expiresAt(new \DateTime('+' . static::CACHE_EXPIRATION));

            return $this->DsaFanApiWrapper->getPlayer($id);
        });
    }
}
