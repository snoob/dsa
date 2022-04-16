<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Application\Cache\CacheableInterface;
use App\Application\Translation\TranslatableInterface;
use App\Application\Translation\Translator;
use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\String\Slugger\AsciiSlugger;

//TODO merge Category and MovieEnum
//@TODO auto resolve la team size depuis le toonProvider = transformer gettoons en mode static

/**
 * @mixin CategoryTagEnum
 * @mixin MovieTagEnum
 */
final class TagEnum extends Enum implements CacheableInterface, TranslatableInterface
{
    use AutoDiscoveredValuesTrait;

    public const ADVENTURER = 'adventurer';

    public const ALADDIN = 'aladdin';

    public const ATLANTIS = 'atlantis';

    public const GARGOYLES = 'gargoyles';

    public const JUNGLE_CRUISE = 'jungle-cruise';

    public const OCEANIC = 'oceanic';

    public const ONWARD = 'onward';

    public const PETER_PAN = 'peter-pan';

    public const TANGLED = 'tangled';

    public const THE_INCREDIBLES = 'the-incredibles';

    public function __toString(): string
    {
        return ucfirst($this->value);
    }

    public function toFilename(Translator $translator): string
    {
        return (new AsciiSlugger())->slug($translator->trans($this))->lower()->toString();
    }

    #[Pure]
    public function getCacheKey(): string
    {
        return self::generateCacheKey($this->value);
    }

    public static function generateCacheKey(string $id): string
    {
        return sprintf('toon.%s', $id);
    }

    #[Pure]
    public function getTranslationKey(): string
    {
        return sprintf('toon.category.%s', $this->getValue());
    }

    public function getFallbackTranslation(): string
    {
        return (string) $this;
    }
}
