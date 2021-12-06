<?php

declare(strict_types=1);

namespace App\Domain\Common;

use function Symfony\Component\String\u;

final class StringUtil
{
    public static function humanize(string $string): string
    {
        return u($string)->title()->replace('-', ' ')->toString();
    }
}
