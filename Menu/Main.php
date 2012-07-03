<?php

namespace Neutron\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Neutron\AdminBundle\AdminEvents;
use Neutron\AdminBundle\Event\ConfigureMenuEvent;

class Main extends ContainerAware
{

    public function menu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('Localhost');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());
        $menu->setUri($this->container->get('request')->getBaseUrl() . '/admin');
        $menu->setChildrenAttribute('id', 'menu');


        $menu->addChild('Dashboard', array(
            'route' => 'neutron_admin.dashboard',
        ));
        
        $category = $menu->addChild('category', array(
            'label' => 'menu.category',
            'uri' => 'javascript;',
            'attributes' => array(
                'class' => 'dropdown',
            ),
            'childrenAttributes' => array(
                'class' => 'menu',
            ),
            'extras' => array(
                'safe_label' => true,
                'translation_domain' => 'NeutronAdminBundle'
            ),
        ));
        
        $category->addChild('category_management', array(
            'label' => 'menu.category_management',
            'route' => 'neutron_admin.category.management',
            'extras' => array(
                'translation_domain' => 'NeutronAdminBundle'
            ),
        ));
        
        $category->addChild('category_create', array(
            'label' => 'menu.category_create',
            'route' => 'neutron_admin.category.create',
            'routeParameters' => array('parentId' => $this->container->get('request')->get('parentId', 0)),
            'display' => false,
            'extras' => array(
                'allowed_roles' => array('None'),
                'translation_domain' => 'NeutronAdminBundle'
            ),
        ));

        $userManagement = $menu->addChild('user_management', array(
            'uri' => 'javascript:;',
            'label' => 'menu.user_management',
            'attributes' => array(
                'class' => 'dropdown',
            ),
            'childrenAttributes' => array(
                'class' => 'menu',
            ),
            'extras' => array(
               'safe_label' => true,
               'translation_domain' => 'NeutronUserBundle'
            )
        ));

        $userManagement->addChild('user_management', array(
            'label' => 'menu.user_management',
            'route' => 'neutron_user_management',
            'extras' => array(
                'translation_domain' => 'NeutronUserBundle'
            ),
        ));
        
        $userManagement->addChild('user_management_add', array(
            'label' => 'menu.user_management.add',
            'route' => 'neutron_user_management_add',
            'display' => false,
            'extras' => array(
                'translation_domain' => 'NeutronUserBundle',
                'allowed_roles' => array('None'),
            ),
        ));
        
        $userManagement->addChild('user_management_edit', array(
            'label' => 'menu.user_management.edit',
            'route' => 'neutron_user_management_edit',
            'routeParameters' => array('rowId' => $this->container->get('request')->get('rowId', 0)),
            'display' => false,
            'extras' => array(
                'translation_domain' => 'NeutronUserBundle',
                'allowed_roles' => array('None'),
            ),
        ));
        
        $userManagement->addChild('user_management_delete', array(
            'label' => 'menu.user_management.delete',
            'route' => 'neutron_user_management_delete',
            'routeParameters' => array('rowId' => $this->container->get('request')->get('rowId', 0)),
            'display' => false,
            'extras' => array(
                'translation_domain' => 'NeutronUserBundle',
                'allowed_roles' => array('None'),
            ),
        ));
        
        $userManagement->addChild('profile_show', array(
            'label' => 'administration.profile.show',
            'route' => 'fos_user_profile_show',
        ));

        $userManagement->addChild('profile_edit', array(
            'label' => 'administration.profile.edit',
            'route' => 'fos_user_profile_edit',
        	'extras' => array(
        		'allowed_roles' => array('GOD'),
        	)
        ));

        $userManagement->addChild('divider', array(
            'extras' => array(
                'divider' => true
            )
        ));

        $userManagement->addChild('security_logout', array(
            'label' => 'administration.profile.logout',
            'route' => 'fos_user_security_logout',
        ));

        $this->container->get('event_dispatcher')
            ->dispatch(AdminEvents::onMenuConfigure, new ConfigureMenuEvent($factory, $menu));

        return $menu;
    }

}