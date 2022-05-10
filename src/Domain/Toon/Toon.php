<?php

declare(strict_types=1);

namespace App\Domain\Toon;

use App\Application\Translation\TranslatableInterface;
use JetBrains\PhpStorm\Pure;

final class Toon implements TranslatableInterface, \Stringable
{
    private string $id;

    /**
     * @var array<string, TagEnum>
     */
    private array $tags;

    private int $speed;

    /**
     * @param array<int, TagEnum> $tags
     */
    #[Pure]
    public function __construct(string $id, array $tags, int $speed)
    {
        $this->id = $id;
        foreach ($tags as $tag) {
            $this->tags[$tag->getValue()] = $tag;
        }
        $this->speed = $speed;
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
     * @return array<string, TagEnum>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    #[Pure]
    public function getTranslationKey(): string
    {
        return sprintf('toon.name.%s', $this->getId());
    }

    public function getFallbackTranslation(): string
    {
        return (string) $this;
    }
}
