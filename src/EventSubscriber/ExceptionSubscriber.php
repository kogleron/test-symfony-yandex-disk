<?php

namespace App\EventSubscriber;

use App\Exception\Yad\MissedToken;
use App\Exception\Yad\RefusedAccess;
use App\Service\Yad\Session;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionSubscriber
 * @package App\EventSubscriber
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'processException'
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function processException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof MissedToken) {
            $this->processMissedToken($event);
            return;
        }
        if ($exception instanceof RefusedAccess) {
            $this->processRefusedAccess();
            return;
        }
        if($exception instanceof RequestException && $exception->getResponse()->getStatusCode() === Response::HTTP_UNAUTHORIZED){
            $this->processUnauthorized();
        }
    }

    private function processMissedToken(GetResponseForExceptionEvent $event)
    {
        $event->setResponse(new RedirectResponse('/auth/login/'));
    }

    private function processUnauthorized()
    {
        throw new RefusedAccess();
    }

    private function processRefusedAccess()
    {
        $this->session->destroy();
    }
}