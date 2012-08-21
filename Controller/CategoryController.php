<?php
namespace Neutron\AdminBundle\Controller;

use Neutron\TreeBundle\Model\TreeNodeInterface;

use Neutron\AdminBundle\Acl\AclManagerInterface;

use Neutron\LayoutBundle\Provider\PluginProvider;

use Neutron\Bundle\DataGridBundle\DataGrid\Provider\ContainerAwareProvider;

use Neutron\AdminBundle\Acl\AclManager;

use Neutron\AdminBundle\AdminEvents;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

use Neutron\AdminBundle\Event\AclObjectEvent;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Neutron\TreeBundle\Model\TreeManagerInterface;

use Neutron\TreeBundle\Tree\TreeModelInterface;

use Neutron\AdminBundle\Form\Handler\Category\AddHandler;

use Neutron\AdminBundle\Form\Type\Category\AddType;

use Symfony\Component\Form\FormFactory;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bridge\Twig\TwigEngine;

use Symfony\Component\DependencyInjection\ContainerAware;

class CategoryController extends ContainerAware
{
    public function indexAction()
    {   
        $mainTree = $this->container->get('neutron.tree')
            ->get($this->container->getParameter('neutron_admin.category.tree_name'));
        
        $grid = $this->container->get('neutron.datagrid')->get('tree_management');
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:index.html.twig', 
                array('mainTree' => $mainTree, 'grid' => $grid)
            );
        
        return new Response($template);
    }
    
    public function createAction($parentId)
    {
        
        $node = $this->getNode($parentId);
        
        $form = $this->container->get('neutron_admin.form.category');
        $handler = $this->container->get('neutron_admin.form.handler.category');
        
        $form->setData($node);
        
        if (null !== $handler->process()){
            return new Response(json_encode($handler->getResult()));
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:create.html.twig', array(
                'form' => $form->createView()    
            ));
        
        return new Response($template);
    }
    
    public function updateAction($nodeId)
    {
        $treeManager = $this->container->get('neutron_tree.manager.factory')
            ->getManagerForClass($this->container->getParameter('neutron_admin.category.tree_data_class'));
        
        $category = $treeManager->findNodeBy(array('id' => $nodeId));
        
        if (!$category){
            throw new NotFoundHttpException();
        }
        
        $pluginProvider = $this->container->get('neutron_layout.plugin_provider');
        $updateRoute = $pluginProvider->get($category->getType())->getUpdateRoute();
        $url = $this->container->get('router')->generate($updateRoute, array('id' => $category->getId()));
        return new RedirectResponse($url);
    }
    
    public function deleteAction($nodeId)
    {
        $tree = $this->container->get('neutron.tree')
            ->get($this->container->getParameter('neutron_admin.category.tree_name'));
        
        $categoryManager = $tree->getManager();
        $aclManager = $this->container->get('neutron_admin.acl.manager');
        
        $node = $categoryManager->findNodeBy(array('id' => $nodeId));
        
        if (!$node){
            throw new NotFoundHttpException();
        }
        
        $pluginProvider = $this->container->get('neutron_layout.plugin_provider');
        $pluginDeleteRoute = $pluginProvider->get($node->getType())->getDeleteRoute();
        
        if ($pluginDeleteRoute){
            $redirectUrl = $this->container->get('router')->generate($pluginDeleteRoute, array('id' => $node->getId()));
            return new RedirectResponse($redirectUrl);
        }
        
        $request = $this->container->get('request');
        
        $operation = $request->get('operation', false);
        
        if ($request->getMethod() == 'POST'){
            $this->doDelete($categoryManager, $aclManager, $node, $operation);
            
            $request->getSession()
                ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.deleted');
        
            $redirectUrl = $this->container->get('router')->generate('neutron_admin.category.management');
        
            return new RedirectResponse($redirectUrl);
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:delete.html.twig', array(
                    'node' => $node
            ));
        
        return new Response($template);
    }
   
    
    protected function doDelete(TreeManagerInterface $categoryManager, 
            AclManagerInterface $aclManager, TreeNodeInterface $node, $operation)
    {
        
        $validOperations = array('delete', 'remove');
        
        if (!in_array($operation, $validOperations)){
            throw new \InvalidArgumentException(sprintf('Operation "%s" is not valid', $operation));
        }
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $em->transactional(function(EntityManager $em) use ($categoryManager, $aclManager, $node, $operation){
            $aclManager->deleteObjectPermissions(ObjectIdentity::fromDomainObject($node));
        
            if ($operation == 'delete'){
                $categoryManager->deleteNode($node);
            } elseif($operation == 'remove'){
                $categoryManager->removeNodeFromTree($node);
            }
        });
    }
    
    protected function getNode($parentId)
    {
        $manager = $this->container->get('neutron_tree.manager.factory')
            ->getManagerForClass($this->container->getParameter('neutron_admin.category.tree_data_class'));
        
        $node = $manager->createNode();
        $parent = $manager->findNodeBy(array('id' => (int) $parentId));
        
        if (!$parent){
            throw new NotFoundHttpException();
        }
        
        $node->setParent($parent);
        
        return $node;
    }
    
 

}
