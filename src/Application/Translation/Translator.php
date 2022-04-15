<?php

declare(strict_types=1);

namespace App\Application\Translation;

use App\Application\String\StringUtil;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface as SymfonyTranslatorInterface;

final class Translator implements TranslatorInterface
{
    private SymfonyTranslatorInterface & TranslatorBagInterface $decorated;

    public function __construct(SymfonyTranslatorInterface & TranslatorBagInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function trans(TranslatableInterface|string $subject, array $parameters = [], string $domain = null, string $locale = null): string
    {
        if ($subject instanceof TranslatableInterface) {
            $translationKey = $subject->getTranslationKey();

            if ($this->decorated->getCatalogue($locale)->has($translationKey)) {
                return $this->decorated->trans($translationKey, [], null, $locale);
            }

            return StringUtil::humanize($subject->getFallbackTranslation());
        }

        return $this->decorated->trans($subject, $parameters, $domain, $locale);
    }
}
