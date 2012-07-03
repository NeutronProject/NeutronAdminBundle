<?php
namespace Neutron\AdminBundle\Form\Handler;

use Neutron\TreeBundle\Tree\TreeModelInterface;

use Neutron\ComponentBundle\Form\Helper\FormHelper;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\HttpFoundation\Response;

use Neutron\UserBundle\Mailer\MailerInterface;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Model\UserManagerInterface;


class AddFormHandler
{
    protected $request;
    protected $router;
    protected $treeManager;
    protected $form;
    protected $formHelper;


    public function __construct(Form $form, FormHelper $formHelper, 
            Request $request, Router $router, TreeManagerInterface $treeManager)
    {
        $this->form = $form;
        $this->formHelper = $formHelper;
        $this->request = $request;
        $this->router = $router;
        $this->treeManager = $treeManager;

    }

    public function process($data)
    {
        $this->form->setData($data);
        
        if ($this->request->isXmlHttpRequest()) {
            
            $this->form->bindRequest($this->request);
            
            if ($this->form->isValid()) {
                $this->onSuccess($data);
                $this->request->getSession()
                    ->getFlashBag()->add('neutron_admin_tree_success', 'tree.flash.created');
                
                $result = array(
                    'success' => true,
                    'redirect_uri' => $this->router->generate('neutron_admin')
                );
                
            } else {
                $result = array(
                    'success' => false,
                    'errors' => $this->formHelper->getErrorMessages($this->form, 'NeutronAdminBundle')
                );
            }
            
            return new Response(json_encode($result));

        }

        return false;
    }

    protected function onSuccess(TreeModelInterface $tree)
    {
       
    }
   
}
