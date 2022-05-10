<?php

namespace App\Domain\GameMode;

use App\Domain\Toon\Toon;

final class Showdown
{
    /**
     * @return array<int, string>
     */
    public function getRequiredToonIds(): array
    {
        return [
            'yzma',
            'kronk',
            'mulan',
            'jasmine',
            'belle',
            'pocahontas',
            'milo-thatch',
            'anna',
        ];
    }
}
