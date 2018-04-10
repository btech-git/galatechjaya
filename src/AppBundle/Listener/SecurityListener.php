<?php

namespace AppBundle\Listener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $securityContext = $this->securityContext;
        
        $menu = array(
            'master' => $securityContext->isGranted('ROLE_MASTER'),
            'report' => $securityContext->isGranted('ROLE_REPORT'),
            'staff' => $securityContext->isGranted('ROLE_ADMIN'),
            'transaction' => $securityContext->isGranted('ROLE_TRANSACTION'),
        );
        
        $session = $event->getRequest()->getSession();
        $session->set('menu', $menu);
        $event->getRequest()->setSession($session);
    }
}
