<?php
namespace Neutron\AdminBundle\Form\Handler;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Doctrine\ORM\EntityManager;

use Neutron\LayoutBundle\Provider\PluginProviderInterface;

use Neutron\TreeBundle\Model\TreeManagerFactoryInterface;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

use Neutron\AdminBundle\Acl\AclManagerInterface;

use Neutron\ComponentBundle\Form\Handler\FormHandlerInterface;

use Neutron\ComponentBundle\Form\Helper\FormHelper;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;

class CategoryHandler implements FormHandlerInterface
{
    protected $em;
    protected $request;
    protected $router;
    protected $translator;
    protected $form;
    protected $formHelper;
    protected $treeManager;
    protected $pluginProvider;
    protected $result;


    public function __construct(EntityManager $em, Form $form, FormHelper $formHelper, 
            Request $request, Router $router, Translator $translator,
            PluginProviderInterface $pluginProvider, TreeManagerFactoryInterface $treeManager, $treeClass)
    {
        $this->em = $em;
        $this->form = $form;
        $this->formHelper = $formHelper;
        $this->request = $request;
        $this->router = $router;
        $this->translator = $translator;
        $this->pluginProvider =$pluginProvider;
        $this->treeManager = $treeManager->getManagerForClass($treeClass);
    }

    public function process()
    {

        if ($this->request->isXmlHttpRequest()) {
            
            $this->form->bind($this->request);
            
            if ($this->form->isValid()) {
                $node = $this->form->getData();
                $this->treeManager->persistAsLastChildOf($node, $node->getParent());

                $route = $this->pluginProvider
                    ->get($this->form->getData()->getType())->getBackendRoute();
                
                $this->request->getSession()
                    ->getFlashBag()->add('neutron.form.success', array(
                        'type' => 'success', 
                        'body' => $this->translator->trans('category.flash.created', array(), 'NeutronAdminBundle')
                    ));
             
                $this->result = array(
                    'success' => true,
                    'redirect_uri' => $this->router->generate($route, array('id' => $this->form->getData()->getId()))
                );
                
                return true;
                
            } else {
                $this->result = array(
                    'success' => false,
                    'errors' => $this->formHelper->getErrorMessages($this->form, 'NeutronAdminBundle')
                );
                
                return false;
            }
        }

    }
    
    public function getResult()
    {
        return $this->result;
    }
   
}
