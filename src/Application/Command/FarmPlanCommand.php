<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Club\ClubProvider;
use App\Domain\GameMode\Exploration\ExplorationModeFactory;
use App\Domain\Player\PlayerProvider;
use App\Domain\Toon\MovieTagEnum;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\ToonProgressProvider;
use App\Domain\Toon\ToonProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:farm:plan',
    description: 'Export your farm plan.',
)]
class FarmPlanCommand extends Command
{
    private ClubProvider $clubProvider;

    private PlayerProvider $playerProvider;

    private ToonProvider $toonProvider;

    private ToonProgressProvider $toonProgressProvider;

    private ExplorationModeFactory $explorationModeFactory;

    public function __construct(
        ClubProvider $clubProvider,
        PlayerProvider $playerProvider,
        ToonProvider $toonProvider,
        ToonProgressProvider $toonProgressProvider,
        ExplorationModeFactory $explorationModeFactory
    ) {
        parent::__construct();
        $this->clubProvider = $clubProvider;
        $this->playerProvider = $playerProvider;
        $this->toonProvider = $toonProvider;
        $this->toonProgressProvider = $toonProgressProvider;
        $this->explorationModeFactory = $explorationModeFactory;
        $this->clubId = '1E50A1ED';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $player = 'AB527917';

        $commandOutputStyle = new CommandOutputStyle($input, $output);
        $stopwatch = new Stopwatch(true);

        $club = $this->clubProvider->find($this->clubId);

        if (null === $club) {
            throw new InvalidArgumentException(sprintf('Club with id %s not found.', $this->clubId));
        }

        $player = $this->playerProvider->find($player);

        if (null === $player) {
            throw new InvalidArgumentException(sprintf('Player with id %s not found.', $player));
        }

        $map = [];

        foreach (TagEnum::instances() as $tag) {
            $map[$tag->getValue()] = 0;
            $mode = $this->explorationModeFactory->create(8, $tag);
            foreach ($this->toonProvider->findByTag($tag) as $toon) {
                $toonProgress = $this->toonProgressProvider->find($player, $toon);
                if ($mode->isToonEligible($toonProgress)) {
                    ++$map[$tag->getValue()];
                }
            }
        }

        foreach (MovieTagEnum::values() as $tag);

        return Command::SUCCESS;
    }
}
