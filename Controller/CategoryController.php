<?php
namespace Neutron\AdminBundle\Controller;

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
        
        $tree = $this->container->get('neutron.tree')
            ->get($this->container->getParameter('neutron_admin.category.tree_name'));
        $manager = $tree->getManager();
        
        $node = $manager->createNode();
        $parent = $manager->findNodeBy(array('id' => (int) $parentId));
        
        if (!$parent){
            throw new NotFoundHttpException();
        }
        
        $form = $this->container->get('neutron_admin.category.form.add');
        $request = $this->container->get('request');
        
        $subscriber = $this->container->get('neutron_admin.form.event_subscriber.category');
        
        $subscriber->setParentNode($parent);
        
        $form->setData(array('general' => $node));
        
        if ($request->isXmlHttpRequest()) {
        
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                $manager->persistAsLastChildOf($node, $parent);
                
                $this->container->get('neutron_admin.acl.manager')
                    ->setObjectPermissions(ObjectIdentity::fromDomainObject($node), $form->get('acl')->getData());
                
                $request->getSession()
                    ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.created');
        
                $result = array(
                    'success' => true,
                    'redirect_uri' => $this->container->get('router')->generate('neutron_admin.category.management')
                );
        
            } else {
                $result = array(
                    'success' => false,
                    'errors' => $this->container->get('neutron_component.form.helper.form_helper')
                        ->getErrorMessages($form, 'NeutronAdminBundle')
                );
            }
        
            return new Response(json_encode($result));
        
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:create.html.twig', array(
                'form' => $form->createView()    
            ));
        
        return new Response($template);
    }
    
    public function updateAction($nodeId)
    {
        
        $tree = $this->container->get('neutron.tree')
            ->get($this->container->getParameter('neutron_admin.category.tree_name'));
        
        $manager = $tree->getManager();
        
        $node = $manager->findNodeBy(array('id' => (int) $nodeId));
        
        if (!$node){
            throw new NotFoundHttpException();
        }
        
        if ($manager->isRoot($node)){
            throw new \RuntimeException('You can NOT modify root node');
        }
        
        $form = $this->container->get('neutron_admin.category.form.add');
        $request = $this->container->get('request');
        
        $subscriber = $this->container->get('neutron_admin.form.event_subscriber.category');
        
        $subscriber->setParentNode($node->getParent());
        
        $form->setData(array(
            'general' => $node, 
            'acl' => $this->container->get('neutron_admin.acl.manager')
                    ->getPermissions(ObjectIdentity::fromDomainObject($node))
        ));
        
        if ($request->isXmlHttpRequest()) {
            
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                
                $manager->updateNode($node);
                
                $this->container->get('neutron_admin.acl.manager')
                    ->setObjectPermissions(ObjectIdentity::fromDomainObject($node), $form->get('acl')->getData());
                
              
                
                $request->getSession()
                    ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.updated');
        
                $result = array(
                    'success' => true,
                    'redirect_uri' => $this->container->get('router')->generate('neutron_admin.category.management')
                );
        
            } else {
                $result = array(
                    'success' => false,
                    'errors' => $this->container->get('neutron_component.form.helper.form_helper')
                        ->getErrorMessages($form, 'NeutronAdminBundle')
                );
            }
        
            return new Response(json_encode($result));
        
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:create.html.twig', array(
                'form' => $form->createView()   
            ));
        
        return new Response($template);
    }
    
    public function deleteAction($nodeId)
    {
        $tree = $this->container->get('neutron.tree')
            ->get($this->container->getParameter('neutron_admin.category.tree_name'));
        $manager = $tree->getManager();
        
        $node = $manager->findNodeBy(array('id' => (int) $nodeId));
        
        if (!$node){
            throw new NotFoundHttpException();
        }
        
        $request = $this->container->get('request');
        

        if ($request->getMethod() == 'POST'){
            
            $operation = $request->get('operation', false);
            
            $validOperations = array('delete', 'remove');
            
            if (!in_array($operation, $validOperations)){
                throw new \InvalidArgumentException(sprintf('Operation "%s" is not valid', $operation));
            }
            
            $this->container->get('neutron_admin.acl.manager')
                ->deleteObjectPermissions(ObjectIdentity::fromDomainObject($node));
            
            if ($operation == 'delete'){
                $manager->deleteNode($node);
            } elseif($operation == 'remove'){
                $manager->removeNodeFromTree($node);
            } 
            
            
            $request->getSession()
                ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.deleted');
            
            $url = $this->container->get('router')->generate('neutron_admin.category.management');
            
            return new RedirectResponse($url);
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:delete.html.twig', array(
                'node' => $node
            ));
        
        return new Response($template);
    }

}
