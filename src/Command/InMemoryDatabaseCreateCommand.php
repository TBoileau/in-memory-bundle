<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class InMemoryDatabaseCreateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'inmemory:database:create';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Create database';

    public function __construct(private string $inMemoryDatabasePath)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($this->inMemoryDatabasePath)) {
            $filesystem->remove($this->inMemoryDatabasePath);
        }

        /** @var string $json */
        $json = json_encode(['collections' => []]);

        $filesystem->dumpFile($this->inMemoryDatabasePath, $json);

        $output->writeln('<info>Database created !</info>');

        return Command::SUCCESS;
    }
}
