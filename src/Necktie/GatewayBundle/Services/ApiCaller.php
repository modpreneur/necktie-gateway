<?php

namespace Necktie\GatewayBundle\Services;

use GuzzleHttp\Client;
use Necktie\GatewayBundle\Logger\Entity\Request;
use Trinity\Bundle\LoggerBundle\Services\ElasticLogService;

/**
 * Class ApiCaller
 * @package Necktie\GatewayBundle\Services
 */
class ApiCaller
{

    /** @var  ElasticLogService */
    protected $elasticLogService;

    /** @var  array */
    protected $clientOptions;


    /**
     * ApiCaller constructor.
     *
     * @param ElasticLogService $elasticLogService
     * @param array $clientOptions
     */
    public function __construct(ElasticLogService $elasticLogService, array $clientOptions = [])
    {
        $this->elasticLogService = $elasticLogService;
        $this->clientOptions     = $clientOptions;
    }


    /**
     * @param array $clientOptions
     *
     * @return $this
     */
    public function setClientOptions(array $clientOptions)
    {
        $this->clientOptions = $clientOptions;

        return $this;
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client($this->clientOptions);
    }


    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @param string $tag
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, array $options = [], string $tag = '')
    {
        $client = $this->getClient();
        $response = $client->request($method, $uri, $options);

        try {
            $request = new Request();
            $request->setUrl($uri);
            $request->setMethod($method);
            $request->setResponse(substr((string)$response->getBody(), 0, 32766));
            $request->setOptions($options);
            $request->setStatus($response->getStatusCode());
            $request->setCreatedAt((new \DateTime())->getTimestamp());
            $request->setTag($tag);

            $this->elasticLogService->writeInto('Request', $request);
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }

        return $response;
    }
}