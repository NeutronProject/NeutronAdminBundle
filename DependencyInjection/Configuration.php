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
 
        $this->addSettingsSection($rootNode);

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

    
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('settings')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('neutron_settings')->end()
                                ->scalarNode('handler')->defaultValue('neutron_admin.form.handler.settings.default')->end()
                                ->scalarNode('name')->defaultValue('settings')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
}
