<?php

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
