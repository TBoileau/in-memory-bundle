<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\DataFixtures;

use Countable;
use Iterator;

/**
 * @template TKey of array-key
 * @template T
 *
 * @template-extends Iterator<TKey,T>
 */
interface FixtureLoaderInterface extends Countable, Iterator
{
}
