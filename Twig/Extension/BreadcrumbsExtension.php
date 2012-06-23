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

use Knp\Menu\ItemInterface;

use Symfony\Component\DependencyInjection\Container;

/**
 * Twig extension
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */

class BreadcrumbsExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * Construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Renders breadcrumbs
     *
     * @param ItemInterface $menu
     * @return string html
     */
    public function render(ItemInterface $menu)
    {
        return $this->container->get('templating')
        	->render('NeutronAdminBundle:Menu:breadcrumbs.html.twig',
        		array('path' => $this->getPath($menu->getCurrentItem())));
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'neutron_admin_breadcrumbs_render' =>
        		new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'neutron_admin_breadcrumbs';
    }

	/**
	 * Gets menu path
	 *
	 * @param ItemInterface $item
	 * @return array
	 */
    protected function getPath(ItemInterface $item)
    {
    	$path = array();
    	$obj = $item;

    	do {
    		$path[] = $obj;
    	} while ($obj = $obj->getParent());

		return array_reverse($path);
	}


}
