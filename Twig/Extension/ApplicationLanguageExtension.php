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
    protected $languages;

    /**
     * Construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->languages = $container->getParameter('neutron_admin.languages');
    }
    
    public function status()
    {
        if (!$this->container->getParameter('neutron_admin.translatable')){
            return;
        }
        
        $key = $this->container->get('session')
            ->get('app_locale',  $this->container->getParameter('locale'));
        
        $language = $this->languages[$key];
        
        return $this->container->get('templating')
            ->render('NeutronAdminBundle:Twig/Extension/ApplicationLanguage:language_status.html.twig',
                    array('language' => $language));
    }
    
    public function getLanguage($key)
    {
        if (!$this->container->getParameter('neutron_admin.translatable')){
            return;
        }
        
        if (!isset($this->languages[$key])){
            throw new \InvalidArgumentException(sprintf('Language key "%s" odes not exist.', $key));
        }
        
        return $this->languages[$key];
    }

    public function getLanguages()
    {

        if (!$this->container->getParameter('neutron_admin.translatable')){
            return;
        }
        
        $currentLanguage = $this->container->get('session')
            ->get('app_locale',  $this->container->getParameter('locale'));

        return $this->container->get('templating')
            ->render('NeutronAdminBundle:Twig/Extension/ApplicationLanguage:language_select.html.twig',
                    array('languages' => $this->languages, 'currentLanguage' => $currentLanguage));
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
