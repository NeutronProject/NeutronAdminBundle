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

use Knp\Menu\Iterator\CurrentItemFilterIterator;

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
        $matcher = $this->container->get('knp_menu.matcher');
        $voter = $this->container->get('neutron_component.menu.voter');
        $voter->setUri($this->container->get('request')->getRequestUri());
        $matcher->addVoter($voter);
        
        $treeIterator = new \RecursiveIteratorIterator(
            new \Knp\Menu\Iterator\RecursiveItemIterator(
                new \ArrayIterator(array($menu))
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator = new \Knp\Menu\Iterator\CurrentItemFilterIterator($treeIterator, $matcher);
        
        $breadcrumbs = array();
        
        foreach ($iterator as $item) {
            $item->setCurrent(true);
            $breadcrumbs = $item->getBreadcrumbsArray();
            break;
        }

        return $this->container->get('templating')
        	->render('NeutronAdminBundle:Menu:breadcrumbs.html.twig',
        		array('breadcrumbs' => $breadcrumbs));
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

}
