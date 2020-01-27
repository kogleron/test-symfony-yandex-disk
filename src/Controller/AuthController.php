<?php

namespace App\Controller;

use App\Exception\Yad\RefusedAccess;
use App\Service\Yad\Api;
use App\Service\Yad\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 *
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends AbstractController
{
    /**
     * @Route("/login/")
     * @param Api $api
     * @return RedirectResponse
     */
    public function loginAction(Api $api):RedirectResponse
    {
        return new RedirectResponse($api->getAuthorizationUrl());
    }

    /**
     * @Route("/logout/")
     * @param Session $session
     * @return RedirectResponse
     */
    public function logoutAction(Session $session):RedirectResponse
    {
        $session->destroy();

        return new RedirectResponse('/auth/login/');
    }

    /**
     * @Route("/token/")
     * @param Api $api
     * @param Request $request
     * @return Response
     * @throws RefusedAccess
     * @internal param Session $session
     */
    public function tokenAction(Api $api, Request $request):Response
    {
        $code = $request->get('code');

        if (empty($code)) {
            throw new RefusedAccess();
        }

        $api->loadAccessTokenByCode($code);

        return new RedirectResponse('/');
    }

}