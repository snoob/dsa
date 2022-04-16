<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

/**
 * @method static self ADVENTURER()
 * @method static self DOWNTOWN_HERO()
 * @method static self OCEANIC()
 */
final class CategoryTagEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const ADVENTURER = 'adventurer';

    public const DOWNTOWN_HERO = 'downtown_hero';

    public const OCEANIC = 'oceanic';
}
