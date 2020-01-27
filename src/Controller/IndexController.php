<?php

namespace App\Controller;

use App\Service\Yad\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController implements AuthenticatedController
{
    /**
     * @param Api $api
     * @return JsonResponse
     */
    public function indexAction(Api $api)
    {
        $resources = $api->getPublicResources();

        return new JsonResponse($resources);
    }
}
