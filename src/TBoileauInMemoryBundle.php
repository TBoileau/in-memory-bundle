<?php

declare(strict_types=1);

namespace TBoileau\InMemoryBundle;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TBoileau\InMemoryBundle\DependencyInjection\InMemoryExtension;

final class TBoileauInMemoryBundle extends Bundle
{
    public function getContainerExtension(): Extension
    {
        if (null === $this->extension) {
            $this->extension = new InMemoryExtension();
        }
        return $this->extension;
    }
}
