<?php

namespace Neutron\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $mainTree = $this->get('neutron.tree')->get('main');

        return $this->render('NeutronAdminBundle:Default:index.html.twig', array(
            'mainTree' => $mainTree        
        ));
    }
    
}
