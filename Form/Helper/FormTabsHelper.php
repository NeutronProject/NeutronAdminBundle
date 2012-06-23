<?php
/*
 * This file is part of NeutronAdminBundle
 *
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle\Form\Helper;


/**
 * Form Helper
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */
use Symfony\Component\Form\Form;

use Neutron\Bundle\AsseticBundle\Controller\AsseticController;

use Symfony\Bundle\TwigBundle\TwigEngine;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Component\Security\Core\SecurityContext;

class FormTabsHelper
{

    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createView(Form $form, $submitBtnOptions, $backBtnOptions, $translationDomain = 'messages')
    {
        $this->container->get('neutron_assetic')
            ->appendJavascript('jquery/plugins/blockui/jquery.blockUI.js');
        
        $this->container->get('neutron_assetic')
            ->appendJavascript('bundles/neutronadmin/js/form-tabs.js');

        return $this->container->get('templating')
            ->render('NeutronAdminBundle:Form:form-tabs.html.twig', array(
                'form' => $form->createView(), 
                'submitBtnOptions' => $submitBtnOptions,
                'backBtnOptions' => $backBtnOptions,
                'translation_domain' => $translationDomain
            ));
    }

}
