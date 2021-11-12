<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\DataFixtures;

use TBoileau\InMemoryBundle\DatabaseInterface;

interface FixtureInterface
{
    public function load(DatabaseInterface $database): void;
}
