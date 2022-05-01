<?php

namespace App\Application\Http;

use App\Application\ClassGenerator;
use App\Application\CodeGenerator\ArgumentList;
use App\Application\CodeGenerator\NewKeyword;
use App\Application\CodeGenerator\NewToon;
use App\Application\String\StringUtil;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonList;
use App\DsaToolkitTagMapper;
use Elao\Enum\Exception\InvalidValueException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Type;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DsaToolkit
{
    private HttpClientInterface $httpClient;

    private DsaToolkitTagMapper $dsaToolkitTagMapper;

    public function __construct(HttpClientInterface $httpClient, DsaToolkitTagMapper $dsaToolkitTagMapper)
    {
        $this->httpClient = $httpClient;
        $this->dsaToolkitTagMapper = $dsaToolkitTagMapper;
    }

    /**
     * @return array<int, Toon>
     */
    public function getToons(): array
    {
        $response = $this->httpClient->request(Request::METHOD_GET, 'https://dsatoolkit.com/ab527917/characters');
        $crawler = (new Crawler($response->getContent()))->filter('.cards > a, div.locked');

        $toons = [];
        foreach ($crawler as $node) {
            $tags = $this->extractTags(explode('; ', $node->firstElementChild->getAttribute('data-categories')));

            if (empty($tags)) {
                continue;
            }

            $toons[] = new Toon(StringUtil::slugify($node->getAttribute('title')), $tags);
        }

        usort($toons, static function (Toon $toon1, Toon $toon2) {
            return $toon1->getId() <=> $toon2->getId();
        });

        return $toons;
    }

    /**
     * @param array<int, string> $input
     *
     * @return array<int, TagEnum>
     */
    private function extractTags(array $input): array
    {
        sort($input);
        $tags = [];

        foreach ($input as $tagId) {
            try {
                $tags[] = $this->dsaToolkitTagMapper->mapTag($tagId);
            } catch (InvalidValueException) {
            }
        }

        return $tags;
    }
}
