<?php

namespace App\EventSubscriber;


use App\Controller\AuthenticatedController;
use App\Exception\Yad\MissedToken;
use App\Service\Yad\Session;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AuthSubscriber
 * @package App\EventSubscriber
 */
class AuthSubscriber implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * AuthSubscriber constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param FilterControllerEvent $event
     * @throws MissedToken
     */
    public function onController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }
        if ($controller instanceof AuthenticatedController && !$this->session->isActive()) {
            throw new MissedToken();
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }
}