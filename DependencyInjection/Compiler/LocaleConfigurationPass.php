<?php
namespace Neutron\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class LocaleConfigurationPass implements CompilerPassInterface
{
    /**
     * Removes stof locale listener
     * 
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection\Compiler.CompilerPassInterface::process()
     */
    public function process(ContainerBuilder $container)
    {
        $isTranslatableEnabled = 
            $container->getDefinition('stof_doctrine_extensions.event_listener.locale')->isPublic();
        
        if ($isTranslatableEnabled){
            $container->setParameter('neutron_admin.translatable', true);
            $container->removeDefinition('stof_doctrine_extensions.event_listener.locale');
        } else {
            $container->setParameter('neutron_admin.translatable', false);
        }
        

    }
}