<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle\Mapping;

use Attribute;

#[Attribute]
final class Column
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $index = false,
        public bool $nullable = false
    ) {
    }
}
