<?php
namespace Neutron\AdminBundle\Controller;

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
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:index.html.twig', array('mainTree' => $mainTree));
        
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
        
        $form->setData($node);
        
        if ($request->isXmlHttpRequest()) {
        
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                $manager->persistAsLastChildOf($node, $parent);
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
        
        $form->setData($node);
        
        if ($request->isXmlHttpRequest()) {
        
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                
                $manager->updateNode($node);
                
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
        
        if ($request->getMethod() == 'POST' && $request->get('delete', false)){
            $manager->deleteNode($node);
            
            $request->getSession()
                ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.updated');
            
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
