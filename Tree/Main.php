<?php
namespace Neutron\AdminBundle\Tree;

use Neutron\TreeBundle\Tree\FactoryInterface;

class Main
{
    
    protected $factory;
    
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    public function create()
    {
        $tree = $this->factory->createTree('main');
        $tree
            ->setDataClass('Neutron\TreeBundle\Entity\Category')
            ->setRootName('Web')
            ->setPlugins(array())
        ;
        
        return $tree;
    }
}