<?php

declare(strict_types=1);

namespace App\Application\Translation;

interface TranslatorInterface
{
    /**
     * @param array<string, string> $parameters
     */
    public function trans(TranslatableInterface|string $subject, array $parameters = [], string $domain = null, string $locale = null): string;
}
