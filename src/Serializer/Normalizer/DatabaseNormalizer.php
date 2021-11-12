<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Serializer\Normalizer;

use TBoileau\InMemoryBundle\Database;
use TBoileau\InMemoryBundle\Factory\CollectionFactoryInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class DatabaseNormalizer extends JsonSerializableNormalizer
{
    /**
     * @param array<string, mixed> $defaultContext
     */
    public function __construct(
        private CollectionFactoryInterface $collectionFactory,
        ?ClassMetadataFactoryInterface $classMetadataFactory = null,
        ?NameConverterInterface $nameConverter = null,
        array $defaultContext = []
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $defaultContext);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return Database::class === $type;
    }

    public function denormalize(mixed $rawDatabase, string $type, ?string $format = null, array $context = []): Database
    {
        /** @var Database $database */
        $database = $context[AbstractNormalizer::OBJECT_TO_POPULATE];

        /** @var array<string, array{elements: array, class: class-string}> $collections */
        /** @phpstan-ignore-next-line */
        $collections = $rawDatabase['collections'];

        foreach ($collections as $name => $rawCollection) {
            $collection = $this->collectionFactory->create($rawCollection['class']);
            $serializer = new Serializer([new ObjectNormalizer(), new GetSetMethodNormalizer()]);
            foreach ($rawCollection['elements'] as $rawElement) {
                $element = $serializer->denormalize($rawElement, $rawCollection['class']);
                $collection->add($element);
            }
            $database->addCollection($name, $collection);
        }

        return $database;
    }
}
