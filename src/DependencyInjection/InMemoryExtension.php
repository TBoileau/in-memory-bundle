<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use TBoileau\InMemoryBundle\DataFixtures\FixtureInterface;

final class InMemoryExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->registerForAutoconfiguration(FixtureInterface::class)->addTag('app.in_memory.fixtures');
    }
}
