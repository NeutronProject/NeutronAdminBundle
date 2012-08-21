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
use Neutron\AdminBundle\Model\SettingsManagerInterface;

class SettingsExtension extends \Twig_Extension
{


    protected $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }
    
    public function getSettings($name, $useCache = true)
    {
        return $this->settingsManager->getOption($name, $useCache);
    }


    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'neutron_settings_get' =>   new \Twig_Function_Method($this, 'getSettings'),
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'neutron_settings_extension';
    }


}
