<?php

/*
 * This file is part of Monsieur Biz' Search plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Provider;

use Doctrine\ORM\EntityManagerInterface;

class DocumentRepositoryProvider
{
    /** @param string[] $documentableClasses */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private array $documentableClasses
    ) {
    }

    public function getRepositories(): array
    {
        $repositories = [];
        foreach ($this->documentableClasses as $class) {
            /** @phpstan-ignore-next-line */
            $repositories[] = $this->entityManager->getRepository($class);
        }

        return $repositories;
    }
}
