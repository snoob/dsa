<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Domain\Common\CacheableInterface;
use App\Domain\Common\TranslatableInterface;
use App\Domain\Common\Translator;
use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @method static self ADVENTURER()
 * @method static self ALADDIN()
 * @method static self ATLANTIS()
 * @method static self GARGOYLES()
 * @method static self JUNGLE_CRUISE()
 * @method static self ONWARD()
 * @method static self PETER_PAN()
 * @method static self TANGLED()
 * @method static self THE_INCREDIBLES()
 */
final class ToonTagEnum extends Enum implements CacheableInterface, TranslatableInterface
{
    use AutoDiscoveredValuesTrait;

    public const ADVENTURER = 'adventurer';

    public const ALADDIN = 'aladdin';

    public const ATLANTIS = 'atlantis';

    public const GARGOYLES = 'gargoyles';

    public const JUNGLE_CRUISE = 'jungle-cruise';

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

    public function getCacheKey(): string
    {
        return sprintf('toon.%s', $this->value);
    }

    public function getTranslationKey(): string
    {
        return sprintf('toon.category.%s', $this->getValue());
    }

    public function getFallbackTranslation(): string
    {
        return (string) $this;
    }
}
