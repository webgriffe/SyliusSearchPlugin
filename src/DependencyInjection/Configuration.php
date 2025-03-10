<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UndefinedMethod
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('monsieur_biz_sylius_search');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            // Files
            ->arrayNode('files')
                ->children()
                    ->scalarNode('search')->isRequired()->end()
                    ->scalarNode('taxon')->isRequired()->end()
                    ->scalarNode('instant')->isRequired()->end()
                ->end()
            ->end()

            // Documentable classes
            ->variableNode('documentable_classes')->end()

            // Grid
            ->arrayNode('grid')
                ->children()

                    // Limits
                    ->arrayNode('limits')
                        ->children()
                            ->arrayNode('taxon')
                                ->performNoDeepMerging()
                                ->integerPrototype()->end()
                                ->isRequired()
                                ->defaultValue([10, 25, 50])
                            ->end()
                            ->arrayNode('search')
                                ->performNoDeepMerging()
                                ->integerPrototype()->end()
                                ->isRequired()
                                ->defaultValue([10, 25, 50])
                            ->end()
                        ->end()
                    ->end()

                    // Default limit
                    ->arrayNode('default_limit')
                        ->children()
                            ->integerNode('taxon')->isRequired()->defaultValue(10)->end()
                            ->integerNode('search')->isRequired()->defaultValue(10)->end()
                            ->integerNode('instant')->isRequired()->defaultValue(10)->end()
                        ->end()
                    ->end()

                    // Sorting
                    ->arrayNode('sorting')
                        ->children()
                            ->arrayNode('taxon')
                                ->performNoDeepMerging()
                                ->scalarPrototype()->end()
                                ->isRequired()
                                ->defaultValue(['name'])
                            ->end()
                            ->arrayNode('search')
                                ->performNoDeepMerging()
                                ->scalarPrototype()->end()
                                ->isRequired()
                                ->defaultValue(['name'])
                            ->end()
                        ->end()
                    ->end()

                    // Filters
                    ->arrayNode('filters')
                        ->children()
                            ->booleanNode('apply_manually')->isRequired()->defaultValue(false)->end()
                            ->booleanNode('use_main_taxon')->isRequired()->defaultValue(false)->end()
                        ->end()
                    ->end()

                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
