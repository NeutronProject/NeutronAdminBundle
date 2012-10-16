<?php
namespace Neutron\AdminBundle\Form\Handler;

use Neutron\AdminBundle\Model\SettingsManagerInterface;

use Neutron\SeoBundle\Model\SeoManagerInterface;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Doctrine\ORM\EntityManager;

use Neutron\ComponentBundle\Form\Helper\FormHelper;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;

class SettingsHandler 
{
    protected $em;
    protected $request;
    protected $router;
    protected $translator;
    protected $form;
    protected $formHelper;
    protected $seoManager;
    protected $settingsManager;
    protected $result;


    public function __construct(EntityManager $em, Form $form, FormHelper $formHelper, Request $request, 
            Router $router, Translator $translator, SeoManagerInterface $seoManager, SettingsManagerInterface $settingsManager)
    {
        $this->em = $em;
        $this->form = $form;
        $this->formHelper = $formHelper;
        $this->request = $request;
        $this->router = $router;
        $this->translator = $translator;
        $this->seoManager = $seoManager;
        $this->settingsManager = $settingsManager;
    }

    public function process()
    {

        if ($this->request->isXmlHttpRequest()) {
            
            $this->form->bind($this->request);
            
            if ($this->form->isValid()) {

                $this->onSuccess();
                $this->result = array(
                    'success' => true,
                    'successMsg' => $this->translator->trans('settings.form.success', array(), 'NeutronAdminBundle')
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
    
    protected function onSuccess()
    {
        $seoManager = $this->seoManager;
        $settingsManager = $this->settingsManager;
        $objectManager = $this->settingsManager->getObjectManager();
        
        
        $general = $this->form->get('general')->getData();
        $searchEngines = $this->form->get('search_engines')->getData();
        $statistic = $this->form->get('statistic')->getData();
        $seo = $this->form->get('seo')->getData();
         
        $data = array_merge($general, $statistic, $searchEngines);
        
        $objectManager->transactional(function($objectManager) use ($seo, $data, $seoManager, $settingsManager){
            $seoManager->updateDefaultSeo($seo);
            $settingsManager->updateSettings($data);
        });
    }
   
}
