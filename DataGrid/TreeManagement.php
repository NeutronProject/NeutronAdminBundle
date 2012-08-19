<?php
namespace Neutron\AdminBundle\DataGrid;

use Doctrine\ORM\Query;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\ORM\EntityManager;

use Neutron\Bundle\DataGridBundle\DataGrid\FactoryInterface;

class TreeManagement
{

    protected $factory;

    protected $em;
    
    protected $translator;
    
    protected $router;

    public function __construct (FactoryInterface $factory, EntityManager $em, 
            Translator $translator, Router $router)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function build ()
    {
        
        /**
         *
         * @var DataGrid $dataGrid
         */
        $dataGrid = $this->factory->createDataGrid('tree_management');
        $dataGrid->setCaption(
            $this->translator->trans('grid.tree_management.title',  array(), 'NeutronAdminBundle')
        )
            ->setAutoWidth(true)
            ->setColNames(array(
                $this->translator->trans('grid.tree_management.title',  array(), 'NeutronAdminBundle'),
                $this->translator->trans('grid.tree_management.slug',  array(), 'NeutronAdminBundle'),
                $this->translator->trans('grid.tree_management.type',  array(), 'NeutronAdminBundle'),
                $this->translator->trans('grid.tree_management.level',  array(), 'NeutronAdminBundle'),
                $this->translator->trans('grid.tree_management.enabled',  array(), 'NeutronAdminBundle'),
                $this->translator->trans('grid.tree_management.displayed',  array(), 'NeutronAdminBundle'),
              

            ))
            ->setColModel(array(
                array(
                    'name' => 't.title', 'index' => 't.title', 'width' => 200, 
                    'align' => 'left', 'sortable' => true, 'search' => true,
                ), 
                    
                array(
                    'name' => 't.slug', 
                    'index' => 't.slug', 
                    'width' => 200, 
                    'align' => 'left', 
                    'sortable' => true,
                    'search' => true,
                ), 
                    
                array(
                    'name' => 't.type', 
                    'index' => 't.type', 
                    'width' => 200, 
                    'align' => 'left', 
                    'sortable' => true,
                    'search' => true,
                ), 
                    
                array(
                    'name' => 't.lvl', 
                    'index' => 't.lvl', 
                    'width' => 200, 
                    'align' => 'left', 
                    'sortable' => true,
                    'search' => true,
                ), 
                        
                array(
                    'name' => 't.enabled', 
                    'index' => 't.enabled', 
                    'width' => 40, 
                    'align' => 'left', 
                    'sortable' => true, 
                    'formatter' => 'checkbox', 
                    'search' => true, 
                    'stype' => 'select',
                    'searchoptions' => array('value' => array(1 => 'enabled', 0 => 'disabled'))
                ),
                array(
                    'name' => 't.displayed', 
                    'index' => 't.displayed', 
                    'width' => 40, 
                    'align' => 'left', 
                    'sortable' => true, 
                    'formatter' => 'checkbox', 
                    'search' => true, 
                    'stype' => 'select',
                    'searchoptions' => array('value' => array(1 => 'enabled', 0 => 'disabled'))
                ),
     
            ))
            ->setHideGrid(true)
            ->setQueryBuilder($this->getQb())
            ->setSortName('t.lvl')
            ->setSortOrder('asc')
            ->enablePager(true)
            ->enableViewRecords(true)
            ->enableSearchButton(true)
            ->enableEditButton(true)
            ->setEditBtnUri($this->router->generate('neutron_admin.category.update', array('nodeId' => '{id}'), true))
            ->enableDeleteButton(true)
            ->setDeleteBtnUri($this->router->generate('neutron_admin.category.delete', array('nodeId' => '{id}'), true))
            ->setQueryHints(array(
                Query::HINT_CUSTOM_OUTPUT_WALKER => 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker',
            ))
            ->setTreeName('main')
        ;


        
        return $dataGrid;
    }

    private function getQb ()
    {
        $conn = $this->em->getConnection();
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(array('t.id', 't.title', 't.slug', 't.type', 't.lvl', 't.enabled', 't.displayed'))
            ->from('NeutronAdminBundle:MainTree', 't')
            ->where($qb->expr()->neq('t.type', $conn->quote('root')))
        ;
        
        return $qb;
    }

}