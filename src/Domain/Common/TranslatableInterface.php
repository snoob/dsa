<?php

declare(strict_types=1);

namespace App\Domain\Common;

interface TranslatableInterface
{
    public function getTranslationKey(): string;

    public function getFallbackTranslation(): string;
}
