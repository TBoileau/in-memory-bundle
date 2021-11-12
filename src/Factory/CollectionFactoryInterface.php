<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Factory;

use TBoileau\InMemoryBundle\Common\Collection\IndexedCollection;

interface CollectionFactoryInterface
{
    /**
     * @param object|class-string $class
     */
    public function create(object|string $class): IndexedCollection;
}
