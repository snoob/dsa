<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Club\Club;
use App\Domain\Player\Player;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonProgress;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DsaApiWrapper
{
    private HttpClientInterface $httpClient;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(HttpClientInterface $dsaHttpClient, UrlGeneratorInterface $urlGenerator)
    {
        $this->httpClient = $dsaHttpClient;
        $this->urlGenerator = $urlGenerator;
    }

    public function getClub(string $clubId): ?Club
    {
        $response = $this->httpClient->request('GET', $this->urlGenerator->generate('dsa_club', ['id' => $clubId]));

        try {
            $crawler = (new Crawler($response->getContent()));
        } catch (HttpExceptionInterface $exception) {
            return null;
        }

        preg_match('/Disney Arena (.*) Club/', $crawler->filter('h1')->innerText(), $matches);

        return new Club(
            $clubId,
            $matches[1],
            new \DateTimeImmutable($crawler->filter('[data-datetime]')->attr('data-datetime')),
            $this->getClubPlayers($crawler)
        );
    }

    public function getToonProgress(Player $player, Toon $toon): ToonProgress
    {
        $response = $this->httpClient->request('GET', $this->urlGenerator->generate('dsa_toon_progress', ['id' => $toon->getId(), 'playerId' => $player->getId()]));

        try {
            $crawler = (new Crawler($response->getContent()))->filter('.dsa-character-card ');
            $gearNode = $crawler->filter('.dsa-character-card__gear-tier');
            $star = (int) $crawler->filter('.dsa-card-rarity__summary-label')->innerText();
            $gear = $gearNode->count() > 0 ? (int) $gearNode->innerText() : 1;
            $level = (int) $crawler->filter('.dsa-card-callout__value')->innerText();
        } catch (HttpExceptionInterface $httpException) {
            $star = 0;
            $gear = 0;
            $level = 0;
        }

        return new ToonProgress($player, $toon, $star, $gear, $level);
    }

    /**
     * @return array<int, Player>
     */
    private function getClubPlayers(Crawler $crawler): array
    {
        $crawler = $crawler->filter('td a')->reduce(static fn (Crawler $node): bool => !empty($node->closest('tr')->filter('td:nth-child(2)')->first()->innerText()));

        $players = new ArrayCollection();

        $start = \strlen('/players/');

        foreach ($crawler->getIterator() as $node) {
            $playerPath = $node->getAttribute('href');

            $response = $this->httpClient->request('GET', $playerPath);
            $players->add(new Player(
                substr($playerPath, $start),
                ucfirst($node->textContent),
                new \DateTimeImmutable((new Crawler($response->getContent()))->filter('[data-datetime]')->attr('data-datetime'))
            ));
        }

        return $players->matching(Criteria::create()->orderBy(['name' => Criteria::ASC]))->toArray();
    }
}