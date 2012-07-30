<?php
namespace Neutron\AdminBundle\EventSubscriber;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Gedmo\Translatable\TranslatableListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This listeners sets the current locale for the TranslatableListener
 *
 * @author Nikolay Georgiev
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    private $translatableListener;
    
    private $defaultLocale;
    
    private $backendLanguages;

    public function __construct(TranslatableListener $translatableListener, $defaultLocale, array $backendLanguages)
    {
        $this->translatableListener = $translatableListener;
        $this->defaultLocale = $defaultLocale;
        $this->backendLanguages = array_keys($backendLanguages);
    }

    /**
     * Set the translation listener locale from the request.
     *
     * This method should be attached to the kernel.request event.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {  
        $preferredBackendLanguage = $event->getRequest()->getPreferredLanguage($this->backendLanguages);

        $session = $event->getRequest()->getSession();
        $event->getRequest()->setLocale($session->get('backend_language', $preferredBackendLanguage));
        
        $this->translatableListener->setTranslatableLocale(
            $session->get('frontend_language', $this->defaultLocale)
        );
    }

    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', -20)),
        );
    }
}