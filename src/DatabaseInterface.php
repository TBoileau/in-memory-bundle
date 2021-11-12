<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle;

use TBoileau\InMemoryBundle\Common\Collection\IndexedCollection;
use TBoileau\InMemoryBundle\Repository\AbstractRepository;

interface DatabaseInterface
{
    /**
     * @param class-string $entity
     *
     * @return AbstractRepository<object>
     *
     * @throw RepositoryDoesNotExistsException
     */
    public function getRepository(string $entity): AbstractRepository;

    public function persist(object $entity): void;

    public function flush(): void;

    /**
     * @return array<string, IndexedCollection>
     */
    public function getCollections(): array;

    public function getCollection(string $entity): IndexedCollection;

    public function addCollection(string $name, IndexedCollection $collection): void;
}
