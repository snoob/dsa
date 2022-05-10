<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Http\DsaFanApiWrapper;
use App\Application\Http\DsaToolKitApiWrapper;
use App\Domain\GameMode\Showdown;
use App\Domain\Toon\ToonList;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:render:showdown_speed',
    description: 'Render showdown toons speed stats.',
)]
class RenderShowdownSpeedCommand extends Command
{
    private DsaToolKitApiWrapper $dsaToolKitApiWrapper;

    private DsaFanApiWrapper $apiWrapper;

    public function __construct(DsaToolKitApiWrapper $dsaToolKitApiWrapper, DsaFanApiWrapper $apiWrapper)
    {
        parent::__construct();
        $this->apiWrapper = $apiWrapper;
        $this->dsaToolKitApiWrapper = $dsaToolKitApiWrapper;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandOutputStyle = new CommandOutputStyle($input, $output);

        $showdown = new Showdown();

        $toons = [];

        $round = 1;
        //$toonIds = $this->dsaToolKitApiWrapper->getShowdownToons($round);
        $toonIds = ['baloo', 'king-triton', 'peter-pan', 'kristoff', 'lotso', 'merida', 'bo-peep', 'sheriff-of-nottingham', 'smee'];

        foreach ($toonIds as $toonId) {
            $toons[] = $this->apiWrapper->getToon($toonId);
        }

        //next api :
        ///(new ToonList())->filterByIds($showdown->getRequiredToonIds())->sortByFastest();
        ///

        $toonList = (new ToonList($toons))->sortByFastest();

        $commandOutputStyle->newLine();
        $table = $commandOutputStyle->createTable();
        $table->setColumnWidths([30, 5]);
        $table->setHeaderTitle(sprintf('Showdown Round %d', $round));
        $table->setHeaders(['Toon', 'Speed']);
        foreach ($toonList->sortByFastest() as $toon) {
            $table->addRow([$toon, $toon->getSpeed()]);
        }
        $table->render();

        return Command::SUCCESS;
    }
}
