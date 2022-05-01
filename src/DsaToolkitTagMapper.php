<?php

namespace App;

use App\Application\String\StringUtil;
use App\Domain\Toon\TagEnum;

final class DsaToolkitTagMapper
{
    public function mapTag(string $tagId): TagEnum
    {
        switch ($tagId) {
            case 'ATLANTIS: THE LOST EMPIRE':
                return TagEnum::ATLANTIS();
            case 'SORCERER\'S CHOSEN: SIEGE ON OLYMPUS':
                return TagEnum::CHOSEN();
            default:
                return TagEnum::get(StringUtil::slugify($tagId));
        }
    }
}
