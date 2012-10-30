<?php
namespace Neutron\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection\Compiler.CompilerPassInterface::process()
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('knp_menu.listener.voters')){
            $container->getDefinition('knp_menu.listener.voters')->clearTag('kernel.event_listener');
        } 
    }
}