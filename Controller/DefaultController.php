<?php

namespace Neutron\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NeutronAdminBundle:Default:index.html.twig', array());
    }
}
