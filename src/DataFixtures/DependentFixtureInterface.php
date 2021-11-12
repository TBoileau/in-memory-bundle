<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\DataFixtures;

interface DependentFixtureInterface
{
    /**
     * @return array<array-key, FixtureInterface>
     */
    public function getDependencies(): array;
}
