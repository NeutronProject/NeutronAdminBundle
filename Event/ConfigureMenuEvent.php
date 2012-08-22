<?php

namespace Neutron\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{

    private $identifier;
    private $factory;
    private $menu;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct($identifier, FactoryInterface $factory, ItemInterface $menu)
    {
        $this->identifier = $identifier;
        $this->factory = $factory;
        $this->menu = $menu;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

}