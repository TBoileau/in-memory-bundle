<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Repository;

use TBoileau\InMemoryBundle\Common\Collection\Collection;
use TBoileau\InMemoryBundle\DatabaseInterface;

/**
 * @template T as object
 */
abstract class AbstractRepository
{
    /**
     * @param class-string $entity
     */
    public function __construct(protected DatabaseInterface $database, protected string $entity)
    {
    }

    /**
     * @return T|null
     */
    public function findOneBy(string $property, mixed $value): ?object
    {
        $data = $this->database->getCollection($this->entity)->findByIndex($property, $value);

        if (0 === $data->count()) {
            return null;
        }

        /* @phpstan-ignore-next-line */
        return $data->first();
    }

    public function findBy(string $property, mixed $value): Collection
    {
        return $this->database->getCollection($this->entity)->findByIndex($property, $value);
    }
}
