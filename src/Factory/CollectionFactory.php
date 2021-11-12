<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Factory;

use TBoileau\InMemoryBundle\Common\Collection\IndexedCollection;
use TBoileau\InMemoryBundle\Resolver\MetadataResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class CollectionFactory implements CollectionFactoryInterface
{
    public function __construct(private MetadataResolverInterface $metadataResolver)
    {
    }

    public function create(object|string $class): IndexedCollection
    {
        $metadata = $this->metadataResolver->resolve($class);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $collection = new IndexedCollection($metadata->class);

        foreach ($metadata->indexes as $index) {
            $column = $metadata->columns[$index];
            $collection->addIndex(
                $column->name,
                static fn (object $entity, $index) => $propertyAccessor->getValue($entity, $index)
            );
        }

        return $collection;
    }
}
