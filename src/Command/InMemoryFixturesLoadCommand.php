<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Command;

use TBoileau\InMemoryBundle\DatabaseInterface;
use TBoileau\InMemoryBundle\DataFixtures\FixtureInterface;
use TBoileau\InMemoryBundle\DataFixtures\FixtureLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InMemoryFixturesLoadCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'inmemory:fixtures:load';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Load fixtures';

    /**
     * @param FixtureLoaderInterface<int, FixtureInterface> $fixtureLoader
     */
    public function __construct(private FixtureLoaderInterface $fixtureLoader, private DatabaseInterface $database)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var FixtureInterface $fixture */
        foreach ($this->fixtureLoader as $fixture) {
            $fixture->load($this->database);
        }

        $output->writeln('<info>Fixtures loaded !</info>');

        return Command::SUCCESS;
    }
}
