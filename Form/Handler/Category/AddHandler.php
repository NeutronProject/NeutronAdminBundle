<?php
namespace Neutron\AdminBundle\Form\Handler\Category;

use Neutron\TreeBundle\Tree\Provider\TreeProviderInterface;

use Neutron\TreeBundle\Tree\TreeModelInterface;

use Neutron\ComponentBundle\Form\Helper\FormHelper;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\HttpFoundation\Response;

use Neutron\UserBundle\Mailer\MailerInterface;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Model\UserManagerInterface;


class AddHandler
{
    
    const MODE_ADD = 'ADD';
    
    const MODE_EDIT = 'EDIT';
    
    protected $request;
    protected $router;
    protected $form;
    protected $formHelper;
    protected $treeManager;


    public function __construct(Form $form, FormHelper $formHelper, 
            Request $request, Router $router, TreeProviderInterface $treeProvider, $treeName)
    {
        $this->form = $form;
        $this->formHelper = $formHelper;
        $this->request = $request;
        $this->router = $router;
        $this->treeManager = $treeProvider->get($treeName)->getManager();
    }

    public function process($mode)
    {
        if ($mode === self::MODE_ADD){
            $node = $this->treeManager->createNode();
        }
        
        $parent = $this->manager->findNodeById($this->request->get('parentId', false));
        
        $this->form->setData($node);
        
        if ($this->request->isXmlHttpRequest()) {
            
            $this->form->bindRequest($this->request);
            
            if ($this->form->isValid()) {
                $this->onValid($node);
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

    protected function onValid(TreeModelInterface $tree)
    {
        
    }
   
}
