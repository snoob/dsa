<?php

declare(strict_types=1);

namespace App\Application\Http;

use App\Domain\Club\Club;
use App\Domain\Player\Player;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonProgress;
use Elao\Enum\Exception\InvalidValueException;
use PhpCsFixer\DocBlock\Tag;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DsaToolKitApiWrapper
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return array<int, Player>
     */
    public function getShowdownToons(int $round): array
    {
        $response = $this->httpClient->request(Request::METHOD_GET, 'https://dsatoolkit.com/ab527917/showdown');

        try {
            $crawler = (new Crawler($response->getContent()));
        } catch (HttpExceptionInterface $exception) {
            return [];
        }

        $showdownToons = [];
        $slugger = new AsciiSlugger();
        $showdownTable = $crawler
            ->filter('.showdown__round:contains(\'' . sprintf('Round %d', $round) . '\')')
            ->closest('.showdown__table');

        foreach ($showdownTable->filter('.showdown__item a')->getIterator() as $node) {
            $toon = (string) $slugger->slug($node->textContent)->lower();

            if ('walloe' === $toon) {
                $toon = 'walle';
            }

            $showdownToons[] =  $toon;
        }

        return $showdownToons;
    }
}
