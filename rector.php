<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // region Symfony Container
    $parameters = $containerConfigurator->parameters();

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/config']);

    // is your PHP version different from the one your refactor to? [default: your PHP version], uses PHP_VERSION_ID format
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_81);

    $parameters->set(
        Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER,
        __DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml'
    );
    // endregion

    $containerConfigurator->import(SymfonySetList::SYMFONY_60);
    $containerConfigurator->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CODE_QUALITY);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION);
    $containerConfigurator->import(SymfonySetList::SYMFONY_STRICT);

    $services = $containerConfigurator->services();

    $services->set(\Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector::class)
        ->call('configure', [[
            \Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector::FROM => 'yaml',
            \Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector::TO => 'php',
        ]])
    ;
};
