<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Common\Collection;

use TBoileau\InMemoryBundle\Common\Collection\Exception\NotAllowedToIndexException;
use TBoileau\InMemoryBundle\Common\Collection\Exception\WrongTypeException;
use Closure;
use JsonSerializable;

interface Collection extends JsonSerializable
{
    /**
     * @throws NotAllowedToIndexException
     * @throws WrongTypeException
     */
    public function addIndex(string $name, Closure $callback): void;

    public function findByIndex(string $index, mixed $value): Collection;
}
