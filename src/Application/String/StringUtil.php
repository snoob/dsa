<?php

declare(strict_types=1);

namespace App\Application\String;

use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

final class StringUtil
{
    public static function humanize(string $string): string
    {
        return u($string)->title()->replace('-', ' ')->toString();
    }

    public static function slugify(string $string): string
    {
        return (new AsciiSlugger())->slug($string)->lower()->toString();
    }
}
