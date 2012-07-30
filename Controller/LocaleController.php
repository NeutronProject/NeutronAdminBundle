<?php
namespace Neutron\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LocaleController extends Controller
{
    public function changeBackendLanguageAction()
    {
        $defaultLocale = $this->container->getParameter('locale');
        
        $locale = $this->getRequest()->get('lang', $defaultLocale);
        
        if (!array_key_exists($locale, $this->container->getParameter('neutron_admin.languages.backend'))){
            throw new \LogicException(sprintf('Language "%s" is not available', $locale));
        }
        
        $this->get('session')->set('backend_language', $locale);
        
        $url = $this->getRequest()->headers->get('referer');
        
        if (!$url){
            $url = $this->get('router')->generate('dashboard');
        }
        
        return new RedirectResponse($url);
    }
    
    public function changeFrontendLanguageAction()
    {
        $defaultLocale = $this->container->getParameter('locale');
        
        $locale = $this->getRequest()->get('lang', $defaultLocale);
        
        if (!array_key_exists($locale, $this->container->getParameter('neutron_admin.languages.frontend'))){
            throw new \LogicException(sprintf('Language "%s" is not available', $locale));
        }
        
        $this->get('session')->set('frontend_language', $locale);
        
        $url = $this->getRequest()->headers->get('referer');
        
        if (!$url){
            $url = $this->get('router')->generate('dashboard');
        }
        
        return new RedirectResponse($url);
    }
    
    
}
