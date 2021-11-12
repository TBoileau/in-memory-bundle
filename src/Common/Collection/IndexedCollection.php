<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Common\Collection;

use TBoileau\InMemoryBundle\Common\Collection\Exception\NotAllowedToIndexException;
use TBoileau\InMemoryBundle\Common\Collection\Exception\WrongTypeException;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ArrayCollection<array-key, object>
 */
final class IndexedCollection extends ArrayCollection implements Collection
{
    /**
     * @var array<string, array<string|int|float|bool|null, array<array-key, int>>>
     */
    private array $indexedElements = [];

    /**
     * @var array<string, callable>
     */
    private array $indexes = [];

    /**
     * @param class-string             $class
     * @param array<string, callable>  $indexes
     * @param array<array-key, object> $elements
     */
    public function __construct(private string $class, array $indexes = [], array $elements = [])
    {
        parent::__construct([]);

        if (count($indexes) > 0) {
            $this->indexes = $indexes;
        }

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * @param object $element
     */
    public function add($element): bool
    {
        if (!is_object($element) || $this->class !== $element::class) {
            throw new WrongTypeException('You cannot add element that is not the right type.');
        }

        parent::add($element);

        $elementIndex = $this->indexOf($element);

        foreach ($this->indexes as $index => $callback) {
            $value = $this->getScalarValue($index, $element);

            if (!isset($this->indexedElements[$index][$value])) {
                $this->indexedElements[$index][$value] = [];
            }

            $this->indexedElements[$index][$value][] = $elementIndex;
        }

        return true;
    }

    /**
     * @param object $element
     */
    public function removeElement($element): bool
    {
        if (!is_object($element) || $this->class !== $element::class) {
            throw new WrongTypeException('You cannot add element that is not the right type.');
        }

        $elementIndex = $this->indexOf($element);

        foreach ($this->indexes as $index => $callback) {
            $value = $this->getScalarValue($index, $element);

            $index = array_search($elementIndex, $this->indexedElements[$index][$value]);

            unset($this->indexedElements[$index][$value][$index]);

            $this->indexedElements[$index][$value] = array_values($this->indexedElements[$index][$value]);
        }

        return parent::removeElement($element);
    }

    public function addIndex(string $name, Closure $callback): void
    {
        if ($this->count() > 0) {
            throw new NotAllowedToIndexException('You cannot index when the collection is already filled.');
        }

        $this->indexes[$name] = static fn (object $data): string|int|float|bool|null => $callback($data, $name);
    }

    public function findByIndex(string $index, mixed $value): IndexedCollection
    {
        return new IndexedCollection(
            $this->class,
            $this->indexes,
            array_filter(
                $this->toArray(),
                fn (int $key): bool => in_array($key, $this->indexedElements[$index][$value] ?? []),
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    /**
     * @return array{elements: array<array-key, object>, class: class-string}
     */
    public function jsonSerialize(): array
    {
        return [
            'elements' => $this->toArray(),
            'class' => $this->class,
        ];
    }

    private function getScalarValue(string $index, object $element): string|int|float|bool|null
    {
        return $this->indexes[$index]($element);
    }
}
