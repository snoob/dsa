<?php

declare(strict_types=1);

namespace App\Application\Cache;

interface CacheableInterface
{
    public static function generateCacheKey(string $id): string;
}
