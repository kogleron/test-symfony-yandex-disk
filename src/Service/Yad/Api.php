<?php

namespace App\Service\Yad;


use App\Exception\Yad\MissedToken;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Api
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var string|null
     */
    private $clientId;
    /**
     * @var string|null
     */
    private $clientPwd;

    /**
     * Api constructor.
     * @param Session $session
     * @param ContainerInterface $container
     * @throws MissedToken
     */
    public function __construct(Session $session, ContainerInterface $container)
    {
        $this->clientId = $container->getParameter('app.yad.clientId');
        $this->clientPwd = $container->getParameter('app.yad.clientPwd');

        if (empty($this->clientId)) {
            throw new \InvalidArgumentException('Need app.yad.clientId');
        }
        if (empty($this->clientPwd)) {
            throw new \InvalidArgumentException('Need app.yad.clientPwd');
        }

        $this->session = $session;
    }

    /**
     * @todo Should be iterator instead
     * @return array
     * @throws MissedToken
     */
    public function getPublicResources():array
    {
        if (!$this->session->isActive()) {
            throw new MissedToken();
        }

        $client = new Client();
        $results = [];
        $offset = 0;
        $limit = 20;

        do {
            $request = new Request(
                'GET',
                'https://cloud-api.yandex.net/v1/disk/resources/public'
                . '?limit=' . $limit
                . '&offset=' . $offset,
                [
                    'Authorization' => 'OAuth ' . $this->session->getUserToken()
                ]
            );

            $response = $client->send($request);

            $data = json_decode($response->getBody(), true);
            if (!empty($data['items'])) {
                $results[] = $data['items'];
            }
            $offset += $limit;
        } while (!empty($data['items']));

        return empty($results) ? [] : array_merge(...$results);
    }

    /**
     * @param string $code
     */
    public function loadAccessTokenByCode(string $code)
    {
        $client = new Client([
                                 'auth' => [
                                     $this->clientId,
                                     $this->clientPwd
                                 ]
                             ]);
        $response = $client->request('POST',
                                     'https://oauth.yandex.ru/token',
                                     [
                                         'form_params' => [
                                             'grant_type' => 'authorization_code',
                                             'code' => $code,
                                         ]
                                     ]);
        $responseData = json_decode($response->getBody(), true);

        if (empty($responseData['access_token'])) {
            throw new \RuntimeException('No access token in response from Yandex: ' . $response->getBody());
        }

        $this->session->start($responseData['access_token']);
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl():string
    {
        return 'https://oauth.yandex.ru/authorize?response_type=code'
        . '&client_id=' . $this->clientId;
    }
}