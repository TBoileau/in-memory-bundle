<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Resolver;

use TBoileau\InMemoryBundle\Exception\MappingMissingException;
use TBoileau\InMemoryBundle\Mapping\Column;
use TBoileau\InMemoryBundle\Mapping\Entity;
use TBoileau\InMemoryBundle\Metadata\Metadata;
use ReflectionClass;

final class MetadataResolver implements MetadataResolverInterface
{
    /**
     * @var array<class-string, Metadata>
     */
    private array $metadatas = [];

    public function resolve(string|object $class): Metadata
    {
        $reflectionClass = new ReflectionClass($class);

        if (!isset($this->metadatas[$reflectionClass->getName()])) {
            $this->metadatas[$reflectionClass->getName()] = $this->createMetadata($reflectionClass);
        }

        return $this->metadatas[$reflectionClass->getName()];
    }

    /**
     * @param ReflectionClass<object> $reflectionClass
     */
    private function createMetadata(ReflectionClass $reflectionClass): Metadata
    {
        $entityAttributes = $reflectionClass->getAttributes(Entity::class);

        if (count($entityAttributes) > 0) {
            /** @var Entity $entity */
            $entity = $entityAttributes[0]->newInstance();
            $metadata = new Metadata($reflectionClass->getName(), $entity);
            foreach ($reflectionClass->getProperties() as $property) {
                $columnAttributes = $property->getAttributes(Column::class);
                if (count($columnAttributes) > 0) {
                    /** @var Column $column */
                    $column = $columnAttributes[0]->newInstance();
                    $metadata->addColumn($property->getName(), $column);
                }
            }

            return $metadata;
        }

        throw new MappingMissingException(sprintf('Mapping for %s is missing.', $reflectionClass->getName()));
    }
}
