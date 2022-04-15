<?php

declare(strict_types=1);

namespace App\Application\Translation;

interface TranslatableInterface
{
    public function getTranslationKey(): string;

    public function getFallbackTranslation(): string;
}
