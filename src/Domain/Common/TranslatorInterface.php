<?php

declare(strict_types=1);

namespace App\Domain\Common;

interface TranslatorInterface
{
    /**
     * @param array<string, string> $parameters
     */
    public function trans(TranslatableInterface|string $subject, array $parameters = [], string $domain = null, string $locale = null): string;
}
