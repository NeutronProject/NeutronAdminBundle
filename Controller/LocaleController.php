<?php
namespace Neutron\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LocaleController extends Controller
{
    public function changeAction()
    {
        if (!$this->container->getParameter('neutron_admin.translatable')){
            throw new \LogicException('Translations are not enabled.');
        }
        
        $defaultLocale = $this->container->getParameter('locale');
        
        $locale = $this->getRequest()->get('lang', $defaultLocale);
        
        if (!array_key_exists($locale, $this->container->getParameter('neutron_admin.languages'))){
            throw new \LogicException(sprintf('Language "%s" is not available', $locale));
        }
        
        $this->get('session')->set('app_locale', $locale);
        
        $url = $this->get('router')->generate('neutron_admin.category.management');
        
        return new RedirectResponse($url);
    }
    
    
}
