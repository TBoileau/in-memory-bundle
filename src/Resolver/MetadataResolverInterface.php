<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Resolver;

use TBoileau\InMemoryBundle\Metadata\Metadata;

interface MetadataResolverInterface
{
    /**
     * @param class-string|object $entity
     */
    public function resolve(string|object $entity): Metadata;
}
