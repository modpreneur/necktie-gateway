<?php

namespace Necktie\GatewayBundle\Gateway\RequestProcessor;

use Exception;
use Necktie\GatewayBundle\Gateway\ApiGateway;
use Necktie\GatewayBundle\Logger\LoggerService;

/**
 * Class HTTPFilter
 * @package Necktie\GatewayBundle\Gateway\Filters
 */
class HTTPProcessor extends BaseProcessor
{
    /**
     * @var ApiGateway
     */
    protected $gateway;


    /**
     * HTTPFilter constructor.
     * @param ApiGateway $apiGateway
     * @param LoggerService $logger
     */
    public function __construct(ApiGateway $apiGateway, LoggerService $logger)
    {
        parent::__construct($logger);
        $this->gateway = $apiGateway;
    }


    /**
     * @param array $message
     *
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function process(array $message = []){

        if (isset($message['header'])) {
            $header = $message['header'];
        } else {
            $header = [];
        }

        if (isset($message['body'])) {
            $body = $message['body'];
        } else {
            $body = null;
        }

        if (isset($message['tag'])) {
            $tag = $message['tag'];
        } else {
            $tag = '';
        }

        if (isset($message['attributes'])) {
            $attributes = $message['attributes'];
        } else {
            $attributes = [];
        }

        try {
            $response = $this
                ->gateway
                ->request(
                    $message['method'],
                    $message['url'],
                    $header,
                    $body,
                    $tag,
                    $attributes
                );

            $this->logger->addRecord($response);

            if ($response['status'] === 'error') {
                echo $response['response'] . PHP_EOL;
                $this->logger->addRecord($response);
                throw new \RuntimeException($response['response']);
            }

            return [
                'status'     => $response['status'],
                'response'   => $response['response'],
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes
            ];
        } catch (Exception $ex) {

            $error =  [
                'status'     => 'error',
                'response'   => $ex->getMessage() . ' ' . $ex->getFile() . ':' . $ex->getLine(),
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes
            ];

            $this->logger->addRecord($error, 500);
            return $error;
        }
    }
}
