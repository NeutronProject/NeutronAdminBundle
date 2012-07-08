<?php
namespace Neutron\AdminBundle\Tree;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Neutron\TreeBundle\Tree\FactoryInterface;

class Main
{
    protected $dataClass;
    
    protected $factory;
    
    protected $router;
    
    protected $translator;
    
    public function __construct($dataClass, FactoryInterface $factory, Router $router,  Translator $translator)
    {
        $this->dataClass = $dataClass;
        $this->factory = $factory;
        $this->router = $router;
        $this->translator = $translator;
    }
    
    public function create()
    {

        $tree = $this->factory->createTree('main');
        $tree
            ->setDataClass($this->dataClass)
            ->setManager($this->factory->createManager($this->dataClass))
            ->setRootName('Web')
            ->addPlugin($this->factory->createPlugin('ui', array('selectLimit' => 1)))
            ->addPlugin($this->factory->createPlugin('contextmenu', array(
                'createBtnOptions' => array(
                    'disabled' => false,
                    'label' => $this->translator->trans('tree.btn.create', array(), 'NeutronAdminBundle'),
                    'uri' => $this->router->generate('neutron_admin.category.create', array('parentId' => '{parentId}'))        
                ), 
                'updateBtnOptions' => array(
                    'disabled' => false,
                    'label' => $this->translator->trans('tree.btn.update', array(), 'NeutronAdminBundle'),
                    'uri' => $this->router->generate('neutron_admin.category.update', array('nodeId' => '{nodeId}'))        
                ), 
                'deleteBtnOptions' => array(
                    'disabled' => false,
                    'label' => $this->translator->trans('tree.btn.delete', array(), 'NeutronAdminBundle'),
                    'uri' => $this->router->generate('neutron_admin.category.delete', array('nodeId' => '{nodeId}'))        
                ), 
            )))
        ;
        
        return $tree;
    }
}