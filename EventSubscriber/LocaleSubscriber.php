<?php
namespace Neutron\AdminBundle\EventSubscriber;

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

    public function __construct(TranslatableListener $translatableListener, $defaultLocale)
    {
        $this->translatableListener = $translatableListener;
        $this->defaultLocale = $defaultLocale;
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
        $this->translatableListener->setTranslatableLocale(
            $event->getRequest()->getSession()->get('app_locale', $this->defaultLocale)
        );
    }

    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}