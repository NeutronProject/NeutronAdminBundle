<?php

namespace Neutron\AdminBundle;

use Neutron\AdminBundle\DependencyInjection\Compiler\MenuPass;

use Neutron\AdminBundle\DependencyInjection\Compiler\LocaleConfigurationPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NeutronAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new LocaleConfigurationPass());
        $container->addCompilerPass(new MenuPass());
    }
}
