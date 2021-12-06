<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Domain\Common\TranslatableInterface;

final class Toon implements TranslatableInterface, \Stringable
{
    private string $id;

    /**
     * @var array<string, ToonTagEnum>
     */
    private array $tags;

    /**
     * @param array<int, ToonTagEnum> $tags
     */
    public function __construct(string $id, array $tags)
    {
        $this->id = $id;
        foreach ($tags as $tag) {
            $this->tags[$tag->getValue()] = $tag;
        }
    }

    public function __toString(): string
    {
        return ucfirst($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, ToonTagEnum>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTranslationKey(): string
    {
        return sprintf('toon.name.%s', $this->getId());
    }

    public function getFallbackTranslation(): string
    {
        return (string) $this;
    }
}
