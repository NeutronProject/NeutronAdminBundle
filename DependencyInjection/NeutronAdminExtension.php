<?php

namespace Neutron\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NeutronAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        foreach (array('services') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
        
        if (!empty($config['category'])) {
            $this->loadCategory($config['category'], $container, $loader);
        }

    }
    
    private function loadCategory(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('category.xml');
    
        $container->setAlias('neutron_admin.category.form.handler.add', $config['form']['handler']);
        
        
        $this->remapParametersNamespaces($config, $container, array(
            'form' => 'neutron_admin.category.form.%s',
            '' => array(
                'tree_data_class' => 'neutron_admin.category.tree_data_class',
                'tree_name' => 'neutron_admin.category.tree_name'       
            )
        ));
    }
    
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }
    
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
