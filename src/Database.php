<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle;

use TBoileau\InMemoryBundle\Common\Collection\IndexedCollection;
use TBoileau\InMemoryBundle\Exception\RepositoryDoesNotExistsException;
use TBoileau\InMemoryBundle\Factory\CollectionFactoryInterface;
use TBoileau\InMemoryBundle\Repository\AbstractRepository;
use TBoileau\InMemoryBundle\Resolver\MetadataResolverInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class Database implements DatabaseInterface
{
    /**
     * @var array<class-string, AbstractRepository<object>>
     */
    private array $repositories = [];

    /**
     * @var array<string, IndexedCollection>
     */
    private array $collections = [];

    public function __construct(
        private string $inMemoryDatabasePath,
        private MetadataResolverInterface $metadataResolver,
        private CollectionFactoryInterface $collectionFactory,
        private SerializerInterface $serializer
    ) {
        $this->init();
    }

    public function getRepository(string $entity): AbstractRepository
    {
        if (!isset($this->repositories[$entity])) {
            throw new RepositoryDoesNotExistsException(sprintf('The repository for %s does not exist.', $entity));
        }

        return $this->repositories[$entity];
    }

    public function persist(object $entity): void
    {
        $metadata = $this->metadataResolver->resolve($entity);

        if (!isset($this->collections[$metadata->entity->name])) {
            $this->collections[$metadata->entity->name] = $this->collectionFactory->create($entity);
        }

        $this->collections[$metadata->entity->name]->add($entity);
    }

    public function flush(): void
    {
        $filesystem = new Filesystem();

        $filesystem->dumpFile(
            $this->inMemoryDatabasePath,
            $this->serializer->serialize(
                $this,
                'json',
                [AbstractNormalizer::ATTRIBUTES => 'collections']
            )
        );
    }

    /**
     * @return array<string, IndexedCollection>
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * @param class-string $entity
     */
    public function getCollection(string $entity): IndexedCollection
    {
        $metadata = $this->metadataResolver->resolve($entity);

        return $this->collections[$metadata->entity->name];
    }

    public function addCollection(string $name, IndexedCollection $collection): void
    {
        $this->collections[$name] = $collection;
    }

    private function init(): void
    {
        $this->serializer->deserialize(
            file_get_contents($this->inMemoryDatabasePath),
            self::class,
            'json',
            [
                AbstractNormalizer::ATTRIBUTES => 'collections',
                AbstractNormalizer::OBJECT_TO_POPULATE => $this,
            ]
        );
    }
}
