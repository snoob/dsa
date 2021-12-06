<?php

declare(strict_types=1);

namespace App\Domain\Common;

use Symfony\Contracts\Cache\TagAwareCacheInterface;

abstract class AbstractProvider
{
    protected const CACHE_EXPIRATION = '24 hours';

    protected TagAwareCacheInterface $cache;

    public function __construct(TagAwareCacheInterface $cache)
    {
        $this->cache = $cache;
    }
}
