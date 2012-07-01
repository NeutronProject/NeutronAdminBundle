<?php
namespace Neutron\AdminBundle\Tree;

use Neutron\TreeBundle\Tree\Plugin\PluginFactoryInterface;

use Neutron\TreeBundle\Tree\FactoryInterface;

class Main
{
    
    protected $factory;
    
    protected $pluginFactory;
    
    public function __construct(FactoryInterface $factory, PluginFactoryInterface $pluginFactory)
    {
        $this->factory = $factory;
        $this->pluginFactory = $pluginFactory;
    }
    
    public function create()
    {
        
        
        $tree = $this->factory->createTree('main');
        $tree
            ->setDataClass('Neutron\AdminBundle\Entity\MainTree')
            ->setRootName('Web')
            ->addPlugin($this->pluginFactory->createPlugin('ui', array('selectLimit' => 1)))
            ->addPlugin($this->pluginFactory->createPlugin('contextmenu', array(
                'enableCreateBtn' => false, 
                'createBtnLabel' => 'Create', 
                'createBtnUri' => 'http://google.com'
            )))
        ;
        
        return $tree;
    }
}