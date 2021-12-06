<?php

declare(strict_types=1);

namespace App\Domain\Common;

interface CacheableInterface
{
    public function getCacheKey(): string;
}
