<?php
/*
 * This file is part of NeutronAdminBundle
 *
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle\Twig\Extension;


/**
 * Twig extension
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */
use Symfony\Component\Form\FormView;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Bundle\TwigBundle\TwigEngine;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContext;

class FormExtension extends \Twig_Extension
{

    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function renderFormTabs(FormView $form, array $options, array $buttons = array())
    {
    	
    	return $this->container->get('templating')
    	    ->render('NeutronAdminBundle:Twig\Extension\Form:form-tabs.html.twig', array(
                'form'    => $form,
                'options' => $this->resolveOptions($options),
                'buttons' => $this->resolveButtons($buttons),
    	    ));
    	
    }
    
    public function renderForm(FormView $form, array $options, array $buttons = array())
    {
    	
    	return $this->container->get('templating')
    	    ->render('NeutronAdminBundle:Twig\Extension\Form:form.html.twig', array(
                'form'    => $form,
                'options' => $this->resolveOptions($options),
                'buttons' => $this->resolveButtons($buttons),
    	    ));
    	
    }


    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'neutron_form_tabs' =>   new \Twig_Function_Method($this, 'renderFormTabs', array('is_safe' => array('html'))),
            'neutron_form' =>   new \Twig_Function_Method($this, 'renderForm', array('is_safe' => array('html'))),
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'neutron_form_extension';
    }
    
    private function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
       
        
        $resolver->setRequired(array(
            'submit_label',
            'translation_domain',
        ));
        
        $resolver->setAllowedTypes(array(
            'submit_label' => 'string',
            'translation_domain'   => 'string',
        ));
        
        $resolver->setDefaults(array(
            'translation_domain' => 'messages',               
        ));
        
        return $resolver->resolve($options);
    }

    private function resolveButtons(array $buttons)
    {
        $resolvedButtons = array();
        
        $resolver = new OptionsResolver();
        
        $resolver->setRequired(array(
            'label', 'url', 'style',     
        ));
        
        $resolver->setAllowedTypes(array(
            'label' => 'string',        
            'url'   => 'string',        
            'style'  => 'string',        
        ));
 
        
        foreach ($buttons as $buttons){
            $resolvedButtons[] = $resolver->resolve($buttons);
        }
        
        return $resolvedButtons;
    }
}
