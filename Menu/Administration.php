<?php
namespace Neutron\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;

use Symfony\Component\DependencyInjection\ContainerAware;

class Administration extends ContainerAware
{
	public function menu(FactoryInterface $factory, array $options)
	{

		$menu = $factory->createItem('root');
		$menu->setCurrentUri($this->container->get('request')->getRequestUri());
		$menu->setChildrenAttribute('class', 'menu');

		$menu->addChild('user_management', array(
			'label' => 'administration.user_management',
			'extras' => array(
				'section' => true,
				'allowed_roles' => array('GOD')
			)
		));

		$menu->addChild('profile_show', array(
			'label' => 'administration.profile.show',
			'route' => 'fos_user_profile_show',
		));

		$menu->addChild('profile_edit', array(
			'label' => 'administration.profile.edit',
			'route' => 'fos_user_profile_edit',
		));

		$menu->addChild('divider', array(
			'extras' => array(
				'divider' => true
			)
		));

		$menu->addChild('security_logout', array(
			'label' => 'administration.profile.logout',
			'route' => 'fos_user_security_logout',
		));


		return $menu;
	}

}