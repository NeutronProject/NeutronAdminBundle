<?php
namespace Neutron\AdminBundle\Doctrine\ORM;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Neutron\ComponentBundle\Doctrine\ORM\Query\TreeWalker\AclWalker;

use Neutron\UserBundle\Model\BackendRoles;

use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\HttpFoundation\Request;

use Neutron\LayoutBundle\Provider\PluginProviderInterface;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Neutron\TreeBundle\Model\TreeNodeInterface;

use Neutron\TreeBundle\Model\TreeManagerFactoryInterface;

use Neutron\AdminBundle\Model\CategoryManagerInterface;

use Doctrine\ORM\EntityManager;

class CategoryManager implements CategoryManagerInterface
{
    
    protected $em;
    
    protected $repository;
    
    protected $cache;
    
    protected $treeManagerFactory;
    
    protected $pluginProvider;
    
    protected $router;
    
    protected $request;
    
    protected $securityContext;
    
    protected $className;
    
    public function __construct(EntityManager $em, TreeManagerFactoryInterface $treeManagerFactory, 
            PluginProviderInterface $pluginProvider, Router $router, Request $request,
            SecurityContext $securityContent, $className)
    {
        $this->em = $em; 
        $this->repository = $em->getRepository($className);
        $this->cache = $em->getConfiguration()->getResultCacheImpl();
        $this->treeManagerFactory = $treeManagerFactory;
        $this->pluginProvider = $pluginProvider;
        $this->router = $router;
        $this->request = $request;
        $this->securityContext = $securityContent;
        $this->className = $className;
    }
    
    public function findCategoryBySlug($slug, $useCache, $locale)
    {
        return $this->repository->findBySlug($slug, $useCache, $locale);
    }
    
    public function buildNavigation()
    {
        $nodes = $this->getPages($this->getUserRoles(), $this->isAdministrativeMode());
        
        $nestedTree = array();
        $l = 0;
    
        if (count($nodes) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();
            foreach ($nodes as $child) {

                
                $item = array(
                    'name' => $child['name'],
                    'label' => $child['title'],
                    'route' => 'neutron_page.distributor',
                    'routeParameters' => array('slug' => $child['slug']),
                    'display' => $child['displayed'],                    
                    'lvl' => $child['lvl'],
                    'children' => array()   
                );
          
                // Number of stack items
                $l = count($stack);
                // Check if we're dealing with different levels
                while($l > 0 && $stack[$l - 1]['lvl'] >= $item['lvl']) {
                    array_pop($stack);
                    $l--;
                }
                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root child
                    $i = count($nestedTree);
            
                    $nestedTree[$i] = $item;
                    $stack[] = &$nestedTree[$i];
                } else {
                    // Add child to parent
                    $i = count($stack[$l - 1]['children']);
                    $stack[$l - 1]['children'][$i] = $item;
                    $stack[] = &$stack[$l - 1]['children'][$i];
                }
            }
        }
    
        if (count($nestedTree) > 0){
            return $nestedTree[0];
        }
        
        return $nestedTree;
        
    }
    
    public function getPages(array $roles = array(), $administrativeMode = false)
    {
        $qb = $this->getTreeManager()->getChildrenQueryBuilder();
        $qb->andWhere('node.enabled = :enabled AND node.displayed = :displayed');
        $qb->setParameters(array('enabled' => true, 'displayed' => true));
    
        $query = $qb->getQuery();
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
    
        if (false === $administrativeMode){
            $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Neutron\\ComponentBundle\\Doctrine\\ORM\\Query\\TreeWalker\\AclWalker'
            );
    
            $query->setHint(
                AclWalker::HINT_ACL_OPTIONS,
                array('roles' => $roles, 'mask' => MaskBuilder::MASK_VIEW)
            );
        }
    
        return $query->getArrayResult();
    }

    
    protected function getUserRoles()
    {
        $user = $this->securityContext->getToken()->getUser();
        
        if ($user != 'anon.') {
            return $user->getRoles();
        } 

        return array('IS_AUTHENTICATED_ANONYMOUSLY');
        
    }
    
    protected function getTreeManager()
    {
        return $this->treeManagerFactory->getManagerForClass($this->className);
    }
    
    protected function isAdministrativeMode()
    {
        if(count(array_intersect($this->getUserRoles(), BackendRoles::getAdministrativeRoles())) > 0){
            return true;
        }
        
        return false;
    }
    
    
}