<?php
namespace Neutron\AdminBundle\Controller;

use Neutron\SeoBundle\Doctrine\ORM\SeoManager;

use Neutron\AdminBundle\Doctrine\ORM\SettingsManager;

use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\DependencyInjection\ContainerAware;

class SettingsController extends ContainerAware
{

    public function indexAction()
    {
        
        $settingsManager = $this->container->get('neutron_admin.settings_manager');
        $seoManager = $this->container->get('neutron_seo.manager');
        
        $form = $this->container->get('neutron_admin.settings.form');
        $handler = $this->container->get('neutron_admin.form.handler.settings');
        //var_dump($settingsManager->getOptionsByGroup('general')); die;
        $form->setData(array(
           'general' => $settingsManager->getOptionsByGroup('general'),
           'seo' => $seoManager->getDefaultSeo(),
           'search_engines' => $settingsManager->getOptionsByGroup('search_engines'),
           'statistic' => $settingsManager->getOptionsByGroup('statistic'),
        ));
        
        if (null !== $handler->process()){
            return new Response(json_encode($handler->getResult()));
        }
        
        $template = $this->container->get('templating')
            ->render('NeutronAdminBundle:Settings:index.html.twig', array(
                'form' => $form->createView()    
            ));
        
        return new Response($template);
    }
    

    public function getSettings()
    {
        return $this->container->get('neutron_admin.settings_manager')->getGroupedOptions();
    }


}
