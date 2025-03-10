<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusSearchPlugin\Fixture\Factory\FilterableFixtureFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class FilterableFixture extends AbstractResourceFixture implements FilterableFixtureInterface
{
    public function __construct(
        EntityManagerInterface $productManager,
        FilterableFixtureFactoryInterface $exampleFactory,
    ) {
        parent::__construct($productManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'monsieurbiz_sylius_search_filterable';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        /**
         * @phpstan-ignore-next-line
         *
         * @psalm-suppress UndefinedInterfaceMethod
         */
        $resourceNode
            ->children()
            ->scalarNode('attribute')->end()
            ->scalarNode('option')->end()
            ->booleanNode('filterable')->defaultValue(true)->end()
        ;
    }
}
