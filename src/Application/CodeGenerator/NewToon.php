<?php

namespace App\Application\CodeGenerator;

use App\Domain\Toon\Toon;

final class NewToon implements \Stringable
{
    private Toon $toon;

    public function __construct(Toon $toon)
    {
        $this->toon = $toon;
    }

    public function __toString(): string
    {
        $tags = $this->toon->getTags();
        $return = sprintf('new Toon(\'%s\', [', $this->toon->getId());

        while (false !== $tag = current($tags)) {
            $return .= new NewEnum($tag);
            $next = next($tags);
            if (false !== $next) {
                $return .= ', ';
            }
        }

        return sprintf('%s])', $return);


    }
}
