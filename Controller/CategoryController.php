<?php
namespace Neutron\AdminBundle\Controller;

use Neutron\AdminBundle\Form\Type\Category\AddType;

use Symfony\Component\Form\FormFactory;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bridge\Twig\TwigEngine;

use Symfony\Component\DependencyInjection\ContainerAware;

class CategoryController extends ContainerAware
{
    public function indexAction()
    {
        $mainTree = $this->container->get('neutron.tree')->get('main');
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:index.html.twig', array('mainTree' => $mainTree));
        
        return new Response($template);
    }
    
    public function createAction($parentId)
    {
        $form = $this->container->get('form.factory')->create(new AddType());
        $handler = null;
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Category:create.html.twig', array(
                'form' => $form->createView()        
            ));
        
        return new Response($template);
    }
    
}
