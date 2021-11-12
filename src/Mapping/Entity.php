<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Mapping;

use Attribute;

#[Attribute]
final class Entity
{
    /**
     * @param class-string $repositoryClass
     */
    public function __construct(public string $name, public string $repositoryClass)
    {
    }
}
