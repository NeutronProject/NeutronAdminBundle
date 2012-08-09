<?php

namespace Neutron\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ArrayNode;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('neutron_admin');

        $this->addLanguageSection($rootNode);
        
        $this->addCategorySection($rootNode);

        return $treeBuilder;
    }
    
    private function addLanguageSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('languages')
                ->isRequired(true)
                ->children()
                    ->arrayNode('backend')
                        ->isRequired(true)
                        ->validate()
                            ->ifTrue(function($v){return empty($v);})
                            ->thenInvalid('You should set at least one backend language')
                        ->end()
                        ->useAttributeAsKey('name')
                            ->prototype('scalar')
                        ->end()
                    ->end()
                    ->arrayNode('frontend')
                        ->isRequired(true)
                        ->validate()
                            ->ifTrue(function($v){ return empty($v);})
                            ->thenInvalid('You should set at least one frontend language')
                        ->end()
                        ->useAttributeAsKey('name')
                            ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    private function addCategorySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('category')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('tree_data_class')->defaultValue('Neutron\AdminBundle\Entity\MainTree')->end()
                        ->scalarNode('tree_name')->defaultValue('main')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('neutron_admin_form_category')->end()
                                ->scalarNode('handler')->defaultValue('neutron_admin.form.handler.category.default')->end()
                                ->scalarNode('name')->defaultValue('neutron_admin_form_category')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
