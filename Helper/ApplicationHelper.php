<?php
namespace Neutron\AdminBundle\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApplicationHelper
{
    protected $session;
    
    protected $defaultLocale;
    
    public function __construct(SessionInterface $session, $defaultLocale)
    {
        $this->session = $session;
        $this->defaultLocale = $defaultLocale;
    }
    
    public function getFrontLocale()
    {
        return $this->session->get('frontend_language', $this->defaultLocale);
    }
}