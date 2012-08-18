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
        
        foreach (array('services', 'acl', 'settings') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
        

        $container->setParameter('neutron_admin.languages.backend', $config['languages']['backend']);
        $container->setParameter('neutron_admin.languages.frontend', $config['languages']['frontend']);
            
        $container->getDefinition('neutron_admin.event_subscriber.locale_subscriber')
            ->setPublic(true)
            ->addTag('kernel.event_subscriber');
        
        
        
        $this->loadCategory($config['category'], $container, $loader);

        $this->loadSettings($config['settings'], $container, $loader);
        
    }
    
    private function loadCategory(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('category.xml');
    
        $container->setAlias('neutron_admin.form.handler.category', $config['form']['handler']);
        
        
        $this->remapParametersNamespaces($config, $container, array(
            'form' => 'neutron_admin.category.form.%s',
            '' => array(
                'tree_data_class' => 'neutron_admin.category.tree_data_class',
                'tree_name' => 'neutron_admin.category.tree_name'       
            )
        )); 
    }
    
    private function loadSettings(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('settings.xml');
    
        $container->setAlias('neutron_admin.form.handler.settings', $config['form']['handler']);
        
        
        $this->remapParametersNamespaces($config, $container, array(
            'form' => 'neutron_admin.settings.form.%s',            
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
