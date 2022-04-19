<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\CodeGenerator\NewToon;
use App\Application\DsaFanApiWrapper;
use App\Application\Http\DsaToolkit;
use App\Application\Spreadsheet\AlexTheme;
use App\Application\Translation\Translator;
use App\Domain\Club\ClubExport;
use App\Domain\Club\ClubProvider;
use App\Domain\Club\Exception\PlayerAlreadyExistsException;
use App\Domain\Club\Exception\PlayerNotExistsException;
use App\Domain\GameMode\AbstractMode;
use App\Domain\GameMode\Exploration\Tier8;
use App\Domain\GameMode\Exploration\Tier9;
use App\Domain\Player\Player;
use App\Domain\Player\PlayerProvider;
use App\Domain\Toon\TagEnum;
use App\Domain\Toon\Toon;
use App\Domain\Toon\ToonList;
use App\Domain\Toon\ToonProgressProvider;
use App\Domain\Toon\ToonProvider;
use Elao\Enum\Exception\InvalidValueException;
use JetBrains\PhpStorm\Pure;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:import:toon',
    description: 'Fill ToonList::getToons() method from toons API.',
)]
class ImportToonDataCommand extends Command
{
    private const TAB = "\t";

    private DsaToolkit $apiWrapper;

    public function __construct(DsaToolkit $apiWrapper)
    {
        $this->apiWrapper = $apiWrapper;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $toons = $this->apiWrapper->getToons();

        $phpFile = new PhpFile();
        $phpFile->setStrictTypes(true);
        $namespace = $phpFile->addNamespace(Helpers::extractNamespace(ToonList::class));
        $namespace->addUse(TagEnum::class);
        $namespace->addUse(Toon::class);
        $class = ClassType::from(ToonList::class);
        $namespace->add($class);
        $method = $class->getMethod('getToons');
        $method->setBody(sprintf('return [%s', PHP_EOL));

        while (false !== $toon = current($toons)) {
            $method->addBody(self::TAB . new NewToon($toon) . (false !== next($toons) ? ',' : ''));
        }
        $method->addBody('];');

        $fileSystem = new Filesystem();
        $fileSystem->dumpFile((new \ReflectionClass(ToonList::class))->getFileName(), (string) $phpFile);

        return Command::SUCCESS;
    }
}
