<?php

namespace App\Service\Yad;


use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Session
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $appSession;
    private $appSessionTokenName = 'yad/user-token';

    public function __construct(SessionInterface $session)
    {
        $this->appSession = $session;
    }

    /**
     * @return bool
     */
    public function isActive():bool
    {
        return !empty($this->appSession->get($this->appSessionTokenName));
    }

    public function getUserToken()
    {
        return $this->appSession->get($this->appSessionTokenName);
    }

    public function destroy()
    {
        $this->appSession->set($this->appSessionTokenName, null);
    }

    public function start(string $accessToken)
    {
        if (empty($accessToken)) {
            throw new RuntimeException('Empty access token');
        }

        $this->appSession->set($this->appSessionTokenName, $accessToken);
    }
}