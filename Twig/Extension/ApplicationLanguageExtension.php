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

use Symfony\Component\DependencyInjection\Container;

/**
 * Twig extension
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */

class ApplicationLanguageExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    
    /**
     * @var array
     */
    protected $backendLanguages;
    
    /**
     * @var array
     */
    protected $frontendLanguages;

    /**
     * Construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->backendLanguages = $container->getParameter('neutron_admin.languages.backend');
        $this->frontendLanguages = $container->getParameter('neutron_admin.languages.frontend');
    }
    
    public function status()
    {
        if (!$this->container->getParameter('neutron_admin.translatable') 
                || count($this->frontendLanguages) < 2){
            return;
        }
        
        $key = $this->container->get('session')
            ->get('frontend_language',  $this->container->getParameter('locale'));
        
        $language = $this->frontendLanguages[$key];
        
        return $this->container->get('templating')
            ->render('NeutronAdminBundle:Twig/Extension/ApplicationLanguage:language_status.html.twig',
                    array('language' => $language));
    }
    
    public function getLanguage($key)
    {
        if (!isset($this->frontendLanguages[$key])){
            throw new \InvalidArgumentException(sprintf('Language key "%s" odes not exist.', $key));
        }
        
        return $this->frontendLanguages[$key];
    }

    public function getLanguages()
    {

        if (count($this->frontendLanguages) < 2 && count($this->backendLanguages) < 2){
            return;
        }
        
        
        $currentBackendLanguage = $this->container->get('session')
            ->get('backend_language',  $this->container->getParameter('locale'));
        
        
        $currentFrontendLanguage = $this->container->get('session')
            ->get('frontend_language',  $this->container->getParameter('locale'));

        return $this->container->get('templating')
            ->render('NeutronAdminBundle:Twig/Extension/ApplicationLanguage:language_select.html.twig',array(
                'backendLanguages'        => $this->backendLanguages, 
                'frontendLanguages'       => $this->frontendLanguages, 
                'currentBackendLanguage'  => $currentBackendLanguage,
                'currentFrontendLanguage' => $currentFrontendLanguage,
            ));
    }
    
    public function getFrontendLanguages()
    {
        if (count($this->frontendLanguages) < 2){
            return array();
        }
        
        return $this->container->getParameter('neutron_admin.languages.frontend');
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'neutron_admin_language_select' =>
        		new \Twig_Function_Method($this, 'getLanguages', array('is_safe' => array('html'))),
                
            'neutron_admin_language_status' =>
        		new \Twig_Function_Method($this, 'status', array('is_safe' => array('html'))),
                
            'neutron_admin_language_get' =>
        		new \Twig_Function_Method($this, 'getLanguage'),
                
            'neutron_language_get' =>
        		new \Twig_Function_Method($this, 'getFrontendLanguages'),
       
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'neutron_admin_application_language';
    }

}
