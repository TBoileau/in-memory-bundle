<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\DataFixtures;

use Traversable;

/**
 * @template TKey of array-key
 * @template T
 *
 * @template-implements FixtureLoaderInterface<TKey,T>
 */
final class FixtureLoader implements FixtureLoaderInterface
{
    /**
     * @var array<array-key, FixtureInterface>
     */
    private array $fixtures = [];

    private int $position = 0;

    /**
     * @param Traversable<array-key, FixtureInterface> $fixtures
     */
    public function __construct(Traversable $fixtures)
    {
        $fixtures = iterator_to_array($fixtures);
        while (count($fixtures) > 0) {
            /**
             * @var int              $index
             * @var FixtureInterface $fixture
             */
            foreach ($fixtures as $index => $fixture) {
                /** @var array<string, string> $interfaces */
                $interfaces = class_implements($fixture);
                /** @var DependentFixtureInterface $fixture */
                if (
                    !in_array(DependentFixtureInterface::class, $interfaces)
                    || count(array_diff($fixture->getDependencies(), array_map('get_class', $this->fixtures)))
                ) {
                    /* @var FixtureInterface $fixture */
                    $this->fixtures[] = $fixture; /* @phpstan-ignore-line */
                    unset($fixtures[$index]);
                }
            }
        }
    }

    public function current(): FixtureInterface
    {
        return $this->fixtures[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->fixtures[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->fixtures);
    }
}
