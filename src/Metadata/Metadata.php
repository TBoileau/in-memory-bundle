<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Metadata;

use TBoileau\InMemoryBundle\Mapping\Column;
use TBoileau\InMemoryBundle\Mapping\Entity;

final class Metadata
{
    /** @var array<string, Column> */
    public array $columns = [];

    /** @var array<array-key, string> */
    public array $indexes = [];

    /**
     * @param class-string $class
     */
    public function __construct(public string $class, public Entity $entity)
    {
    }

    public function addColumn(string $property, Column $column): void
    {
        $this->columns[$property] = $column;

        if ($column->index) {
            $this->indexes[] = $property;
        }
    }
}
