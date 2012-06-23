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
use Symfony\Component\Security\Core\SecurityContext;

class AclExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    protected $securityContext;


    /**
     * Construct
     *
     * @param SecurityContext $securityContext
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Performs acl check if user has access to resource
     *
     * @return boolean
     */
    public function isAllowed($roles)
    {
    	if (false === $roles){
    		return true;
    	}

        return (count(array_intersect($roles, $this->getUserRoles())) > 0);
    }


    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'neutron_is_allowed' =>   new \Twig_Function_Method($this, 'isAllowed'),
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'neutron_language_extension';
    }

    protected function getUserRoles()
    {
    	$user = $this->securityContext->getToken()->getUser();

    	if ($user == 'anon.'){
    		return array();
    	}

    	return $user->getRoles();
    }
}
